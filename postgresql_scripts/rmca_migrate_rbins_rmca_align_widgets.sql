-- Function: public.rmca_migrate_rbins_rmca_align_widgets()

-- DROP FUNCTION public.rmca_migrate_rbins_rmca_align_widgets();

CREATE OR REPLACE FUNCTION public.rmca_migrate_rbins_rmca_align_widgets()
  RETURNS integer AS
$BODY$
    Declare returned int;
    
    BEGIN
    returned:=-1;
    delete from darwin2.my_widgets;

INSERT INTO darwin2.my_widgets(
             user_ref, category, group_name, order_by, col_num, mandatory, 
            visible, opened, color, is_available, icon_ref, title_perso, 
            collections, all_public)
SELECT users.id, category, group_name, order_by, col_num, mandatory, 
       visible, opened, color, is_available, icon_ref, title_perso, 
       collections, all_public
  FROM darwin2.users cross join 
  (select * from darwin2.my_widgets_rmca
  where user_ref=1) b;

	returned:=0;
      return returned;


      
    END;
    $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.rmca_migrate_rbins_rmca_align_widgets()
  OWNER TO postgres;
