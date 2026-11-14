-- Procedure acc_transaction (Set Status Proses & Tolak Pengajuan Lain)
CREATE OR REPLACE PROCEDURE public.acc_transaction(IN p_request_id integer, IN p_product_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE requests
    SET status = 'PROCESS',
        updated_at = CURRENT_TIMESTAMP
    WHERE transaction_id = p_request_id;

    UPDATE requests
    SET status = 'CANCELLED',
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_product_id 
      AND transaction_id <> p_request_id;
END;
$$;

--Procedure finish_transaction (Selesaikan Transaksi & Update Status Barang)
CREATE OR REPLACE PROCEDURE public.finish_transaction(IN p_request_id integer, IN p_product_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE requests
    SET status = 'COMPLETED',
        completed_date = CURRENT_DATE,
        updated_at = CURRENT_TIMESTAMP
    WHERE transaction_id = p_request_id;

    UPDATE products
    SET status = 'Terjual',
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_product_id;
END;
$$;

-- Procedure Tambahan: cancel_transaction (Batalkan Transaksi & Kembalikan Stok/Status Barang)
CREATE OR REPLACE PROCEDURE public.cancel_transaction(IN p_request_id integer, IN p_product_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE requests
    SET status = 'CANCELLED',
        updated_at = CURRENT_TIMESTAMP
    WHERE transaction_id = p_request_id;

    UPDATE products
    SET status = 'Tersedia',
        updated_at = CURRENT_TIMESTAMP
    WHERE product_id = p_product_id;
END;
$$;

