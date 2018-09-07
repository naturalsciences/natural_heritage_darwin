SET search_path = darwin2, public;

\i ../createfunctions.sql



CREATE trigger trg_reset_last_flag_informative_workflow AFTER DELETE
        ON informative_workflow FOR EACH ROW
        EXECUTE PROCEDURE fct_informative_reset_last_flag();
