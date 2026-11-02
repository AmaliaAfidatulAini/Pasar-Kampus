CREATE OR REPLACE PROCEDURE public.acc_transaction(IN p_request_id integer, IN p_product_id integer)
    LANGUAGE plpgsql
    AS $$
BEGIN
    UPDATE requests
    SET request_status = 'proses'
    WHERE transaction_id = p_request_id;

    UPDATE requests
    SET request_status = 'ditolak'
    WHERE product_id = p_product_id 
      AND transaction_id <> p_request_id;
END;
$$;