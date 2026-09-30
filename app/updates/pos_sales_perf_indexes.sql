-- POS Sales list performance indexes
-- Run against the `kg_coffee` database (table prefix: sma_)
-- Safe to run once. If an index already exists, MySQL errors on that line only
-- (check with: SHOW INDEX FROM sma_sales;) — skip any you already have.

-- getSales() filter: WHERE pos = 1 [AND warehouse_id = ?]
-- Also serves COUNT(*) as a narrow covering index scan instead of reading full rows.
ALTER TABLE sma_sales          ADD INDEX idx_pos_wh (pos, warehouse_id);

-- getSales() filter for non-privileged users: WHERE pos = 1 AND created_by = ?
ALTER TABLE sma_sales          ADD INDEX idx_pos_created (pos, created_by);

-- Sidebar "expiring stock" badge count (get_expiring_qty_alerts)
ALTER TABLE sma_purchase_items ADD INDEX idx_expiry (expiry, quantity_balance);

-- Optional: only if sma_sales.customer_id is NOT already indexed (it backs the
-- LEFT JOIN companies ON companies.id = sales.customer_id). Check first:
--   SHOW INDEX FROM sma_sales WHERE Column_name = 'customer_id';
-- ALTER TABLE sma_sales       ADD INDEX idx_customer (customer_id);

-- Note: the low-stock badge (get_total_qty_alerts) uses `quantity <= alert_quantity`,
-- a column-to-column comparison that no index can optimize. It is handled in code by
-- caching the count for 5 minutes (app/models/Site.php), not by an index.
