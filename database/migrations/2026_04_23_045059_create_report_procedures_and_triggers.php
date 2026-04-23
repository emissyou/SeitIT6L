<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS trg_delivery_details_before_insert;
            DROP TRIGGER IF EXISTS trg_delivery_details_before_update;
            DROP TRIGGER IF EXISTS trg_delivery_details_after_insert;
            DROP TRIGGER IF EXISTS trg_delivery_details_after_update;
            DROP TRIGGER IF EXISTS trg_delivery_details_after_delete;
            DROP TRIGGER IF EXISTS trg_sale_details_before_insert;
            DROP TRIGGER IF EXISTS trg_sale_details_before_update;
            DROP TRIGGER IF EXISTS trg_sale_details_after_insert;
            DROP TRIGGER IF EXISTS trg_sale_details_after_update;
            DROP TRIGGER IF EXISTS trg_sale_details_after_delete;

            CREATE TRIGGER trg_delivery_details_before_insert
            BEFORE INSERT ON delivery_details
            FOR EACH ROW
            BEGIN
                SET NEW.subtotal = NEW.quantity * NEW.unit_cost;
            END;

            CREATE TRIGGER trg_delivery_details_before_update
            BEFORE UPDATE ON delivery_details
            FOR EACH ROW
            BEGIN
                SET NEW.subtotal = NEW.quantity * NEW.unit_cost;
            END;

            CREATE TRIGGER trg_delivery_details_after_insert
            AFTER INSERT ON delivery_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock + NEW.quantity
                WHERE fuel_id = NEW.fuel_id;
            END;

            CREATE TRIGGER trg_delivery_details_after_update
            AFTER UPDATE ON delivery_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock + NEW.quantity - OLD.quantity
                WHERE fuel_id = NEW.fuel_id;
            END;

            CREATE TRIGGER trg_delivery_details_after_delete
            AFTER DELETE ON delivery_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock - OLD.quantity
                WHERE fuel_id = OLD.fuel_id;
            END;

            CREATE TRIGGER trg_sale_details_before_insert
            BEFORE INSERT ON sale_details
            FOR EACH ROW
            BEGIN
                SET NEW.amount = NEW.quantity * NEW.unit_price;
            END;

            CREATE TRIGGER trg_sale_details_before_update
            BEFORE UPDATE ON sale_details
            FOR EACH ROW
            BEGIN
                SET NEW.amount = NEW.quantity * NEW.unit_price;
            END;

            CREATE TRIGGER trg_sale_details_after_insert
            AFTER INSERT ON sale_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock - NEW.quantity
                WHERE fuel_id = NEW.fuel_id;
            END;

            CREATE TRIGGER trg_sale_details_after_update
            AFTER UPDATE ON sale_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock - NEW.quantity + OLD.quantity
                WHERE fuel_id = NEW.fuel_id;
            END;

            CREATE TRIGGER trg_sale_details_after_delete
            AFTER DELETE ON sale_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock + OLD.quantity
                WHERE fuel_id = OLD.fuel_id;
            END;

            DROP PROCEDURE IF EXISTS sp_get_current_stock_summary;
            DROP PROCEDURE IF EXISTS sp_get_shift_summary;
            DROP PROCEDURE IF EXISTS sp_get_customer_credit_report;

            CREATE PROCEDURE sp_get_current_stock_summary()
            BEGIN
                SELECT
                    f.id AS fuel_id,
                    f.name AS fuel_name,
                    i.current_stock,
                    i.capacity,
                    IF(i.capacity > 0, ROUND(i.current_stock / i.capacity * 100, 1), 0) AS percent_full,
                    f.price_per_liter
                FROM fuels f
                LEFT JOIN inventories i ON i.fuel_id = f.id;
            END;

            CREATE PROCEDURE sp_get_shift_summary(IN target_date DATE)
            BEGIN
                SELECT
                    d.id AS daily_sale_id,
                    d.sales_date,
                    COALESCE(SUM(sd.quantity), 0) AS total_liters,
                    d.gross_sales,
                    d.net_sales,
                    d.total_credit,
                    d.total_discount,
                    d.status
                FROM daily_sales d
                LEFT JOIN sale_details sd ON sd.daily_sale_id = d.id
                WHERE d.sales_date = target_date
                GROUP BY d.id, d.sales_date, d.gross_sales, d.net_sales, d.total_credit, d.total_discount, d.status;
            END;

            CREATE PROCEDURE sp_get_customer_credit_report()
            BEGIN
                SELECT
                    c.id AS credit_id,
                    cu.id AS customer_id,
                    CONCAT(cu.first_name, ' ', COALESCE(cu.middle_name, ''), ' ', cu.last_name) AS customer_name,
                    cu.contact_number,
                    c.amount AS credit_amount,
                    c.balance,
                    c.status,
                    COALESCE(SUM(p.amount_paid), 0) AS total_paid
                FROM credits c
                JOIN customers cu ON cu.id = c.customer_id
                LEFT JOIN payments p ON p.credit_id = c.id
                GROUP BY c.id, cu.id, cu.first_name, cu.middle_name, cu.last_name, cu.contact_number, c.amount, c.balance, c.status;
            END;
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS trg_delivery_details_before_insert;
            DROP TRIGGER IF EXISTS trg_delivery_details_before_update;
            DROP TRIGGER IF EXISTS trg_delivery_details_after_insert;
            DROP TRIGGER IF EXISTS trg_delivery_details_after_update;
            DROP TRIGGER IF EXISTS trg_delivery_details_after_delete;
            DROP TRIGGER IF EXISTS trg_sale_details_before_insert;
            DROP TRIGGER IF EXISTS trg_sale_details_before_update;
            DROP TRIGGER IF EXISTS trg_sale_details_after_insert;
            DROP TRIGGER IF EXISTS trg_sale_details_after_update;
            DROP TRIGGER IF EXISTS trg_sale_details_after_delete;
            DROP PROCEDURE IF EXISTS sp_get_current_stock_summary;
            DROP PROCEDURE IF EXISTS sp_get_shift_summary;
            DROP PROCEDURE IF EXISTS sp_get_customer_credit_report;
        SQL);
    }
};
