SET search_path = darwin2, public;

\i ../createfunctions.sql

ALTER TABLE users_workflow RENAME TO informative_workflow ;
DELETE FROM informative_workflow WHERE referenced_relation != 'lithology';
UPDATE informative_workflow SET status = 'checked' WHERE status = 'published';
ALTER TABLE informative_workflow add formated_name varchar not null default 'anonymous' ;
ALTER TABLE informative_workflow add is_last boolean not null default true;
ALTER TABLE informative_workflow ALTER user_ref TYPE integer ;
ALTER TABLE informative_workflow ALTER user_ref DROP NOT NULL ;
ALTER TABLE informative_workflow ALTER status set default 'suggestion' ;

ALTER TABLE informative_workflow drop constraint pk_users_workflow ;
ALTER TABLE informative_workflow add constraint pk_informative_workflow primary key (id) ;

ALTER TABLE informative_workflow drop constraint fk_users_workflow_users ;
ALTER TABLE informative_workflow add constraint fk_informative_workflow_users foreign key (user_ref) references users(id) ON DELETE CASCADE;
ALTER SEQUENCE users_workflow_id_seq RENAME TO informative_workflow_id_seq ;

COMMENT on COLUMN informative_workflow.formated_name is 'used to allow non registered user to add a workflow' ;
COMMENT on COLUMN informative_workflow.is_last is 'a flag witch allow us to know if the workflow for this referenced_relation/record id is the latest' ;

alter trigger trg_chk_ref_record_users_workflow ON informative_workflow RENAME TO trg_chk_ref_record_informative_workflow ;
ALTER INDEX idx_users_workflow_user_status RENAME TO idx_informative_workflow_user_status ;


CREATE trigger trg_chk_is_last_informative_workflow BEFORE INSERT
	ON informative_workflow FOR EACH ROW
	EXECUTE PROCEDURE fct_remove_last_flag();

GRANT USAGE, SELECT ON informative_workflow_id_seq TO d2viewer;
GRANT INSERT, UPDATE ON informative_workflow TO d2viewer;

CREATE INDEX CONCURRENTLY idx_informative_workflow_user_ref on informative_workflow(user_ref);

