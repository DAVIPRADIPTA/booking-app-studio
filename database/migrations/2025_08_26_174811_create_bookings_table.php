<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function indexExists(string $table, string $index): bool
    {
        $db = DB::getDatabaseName();

        $row = DB::selectOne(
            "SELECT COUNT(1) AS cnt
             FROM information_schema.statistics
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$db, $table, $index]
        );

        return ($row && ($row->cnt ?? $row->CNT ?? $row->Cnt ?? 0) > 0);
    }

    public function up(): void
    {
        // If table does not exist — create full table according to model
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');

                $table->string('contact_name');
                $table->string('whatsapp_number');
                $table->date('booking_date');
                $table->string('booking_time');
                $table->string('session_name');
                $table->string('package_name');
                $table->json('selected_backgrounds')->nullable();
                $table->json('selected_extra_items')->nullable();
                $table->integer('total_price')->nullable();

                // Payment
                $table->enum('payment_method', ['cash', 'transfer'])->default('transfer');
                $table->text('notes')->nullable();
                $table->enum('status', [
                    'waiting_payment',
                    'pending_verification',
                    'booked',
                    'completed',
                    'cancelled'
                ])->default('waiting_payment');

                $table->timestamp('payment_deadline')->nullable();
                $table->string('payment_proof')->nullable();

                // Cancellation
                $table->boolean('cancellation_requested')->default(false);
                $table->timestamp('cancellation_requested_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->decimal('refund_amount', 15, 2)->nullable();
                $table->string('refund_proof')->nullable();

                // Baby fields
                $table->string('baby_name')->nullable();
                $table->string('baby_age')->nullable();

                // Auto cancel
                $table->timestamp('auto_cancelled_at')->nullable();

                $table->timestamps();

                // Indexes
                $table->index(['booking_date', 'booking_time'], 'idx_booking_date_time');
                $table->index('status', 'idx_status');
                $table->index('customer_id', 'idx_customer_id');
            });

            return;
        }

        // If table exists, make sure needed columns are present (add if missing)
        // 1) Ensure columns exist
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'contact_name')) {
                $table->string('contact_name')->after('customer_id');
            }
            if (!Schema::hasColumn('bookings', 'whatsapp_number')) {
                $table->string('whatsapp_number')->nullable()->after('contact_name');
            }
            if (!Schema::hasColumn('bookings', 'booking_date')) {
                $table->date('booking_date')->nullable()->after('whatsapp_number');
            }
            if (!Schema::hasColumn('bookings', 'booking_time')) {
                $table->string('booking_time')->nullable()->after('booking_date');
            }
            if (!Schema::hasColumn('bookings', 'session_name')) {
                $table->string('session_name')->nullable()->after('booking_time');
            }
            if (!Schema::hasColumn('bookings', 'package_name')) {
                $table->string('package_name')->nullable()->after('session_name');
            }
            if (!Schema::hasColumn('bookings', 'selected_backgrounds')) {
                $table->json('selected_backgrounds')->nullable()->after('package_name');
            }
            if (!Schema::hasColumn('bookings', 'selected_extra_items')) {
                $table->json('selected_extra_items')->nullable()->after('selected_backgrounds');
            }
            if (!Schema::hasColumn('bookings', 'total_price')) {
                $table->integer('total_price')->nullable()->after('selected_extra_items');
            }

            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'transfer'])->default('transfer')->after('total_price');
            }

            if (!Schema::hasColumn('bookings', 'notes')) {
                $table->text('notes')->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('bookings', 'status')) {
                $table->enum('status', [
                    'waiting_payment',
                    'pending_verification',
                    'booked',
                    'completed',
                    'cancelled'
                ])->default('waiting_payment')->after('notes');
            }

            if (!Schema::hasColumn('bookings', 'payment_deadline')) {
                $table->timestamp('payment_deadline')->nullable()->after('status');
            }

            if (!Schema::hasColumn('bookings', 'payment_proof')) {
                $table->string('payment_proof')->nullable()->after('payment_deadline');
            }

            if (!Schema::hasColumn('bookings', 'cancellation_requested')) {
                $table->boolean('cancellation_requested')->default(false)->after('payment_proof');
            }
            if (!Schema::hasColumn('bookings', 'cancellation_requested_at')) {
                $table->timestamp('cancellation_requested_at')->nullable()->after('cancellation_requested');
            }
            if (!Schema::hasColumn('bookings', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancellation_requested_at');
            }
            if (!Schema::hasColumn('bookings', 'refund_amount')) {
                $table->decimal('refund_amount', 15, 2)->nullable()->after('cancellation_reason');
            }
            if (!Schema::hasColumn('bookings', 'refund_proof')) {
                $table->string('refund_proof')->nullable()->after('refund_amount');
            }
            if (!Schema::hasColumn('bookings', 'auto_cancelled_at')) {
                $table->timestamp('auto_cancelled_at')->nullable()->after('refund_proof');
            }
            if (!Schema::hasColumn('bookings', 'baby_name')) {
                $table->string('baby_name')->nullable()->after('auto_cancelled_at');
            }
            if (!Schema::hasColumn('bookings', 'baby_age')) {
                $table->string('baby_age')->nullable()->after('baby_name');
            }
        });

        // 2) Make sure indexes exist (check via information_schema)
        if (!$this->indexExists('bookings', 'idx_booking_date_time')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index(['booking_date', 'booking_time'], 'idx_booking_date_time');
            });
        }

        if (!$this->indexExists('bookings', 'idx_status')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('status', 'idx_status');
            });
        }

        if (!$this->indexExists('bookings', 'idx_customer_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->index('customer_id', 'idx_customer_id');
            });
        }

        // 3) Normalize status values: map legacy 'pending' -> 'waiting_payment'
        //    (do this BEFORE attempting to restrict enum values in a produced ALTER)
        DB::statement("UPDATE bookings SET status = 'waiting_payment' WHERE status = 'pending'");

        // 4) If payment_deadline is null for waiting_payment rows, set it to now + 10 minutes
        DB::statement(
            "UPDATE bookings
             SET payment_deadline = DATE_ADD(NOW(), INTERVAL 10 MINUTE)
             WHERE status = 'waiting_payment' AND (payment_deadline IS NULL OR payment_deadline = '0000-00-00 00:00:00')"
        );
    }

    public function down(): void
    {
        // If table was created by this migration (impossible to detect reliably),
        // safe approach: remove the columns we added if they exist and remove indexes.
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (Schema::hasColumn('bookings', 'payment_proof')) {
                    $table->dropColumn('payment_proof');
                }
                if (Schema::hasColumn('bookings', 'auto_cancelled_at')) {
                    $table->dropColumn('auto_cancelled_at');
                }
                if (Schema::hasColumn('bookings', 'refund_amount')) {
                    // keep it if you want; we will drop it as part of revert
                    $table->dropColumn('refund_amount');
                }
                // other drops are possible but be careful in production
            });

            // drop indexes if present
            if ($this->indexExists('bookings', 'idx_booking_date_time')) {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->dropIndex('idx_booking_date_time');
                });
            }
            if ($this->indexExists('bookings', 'idx_status')) {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->dropIndex('idx_status');
                });
            }
            if ($this->indexExists('bookings', 'idx_customer_id')) {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->dropIndex('idx_customer_id');
                });
            }
        }
    }
};
