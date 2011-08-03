create domain full_text_language as varchar default 'simple' not null
       constraint full_text_language_chk
                  check (VALUE in ('danish', 'dutch', 'english', 'finnish', 'french', 'german', 'hungarian', 'italian', 'norwegian', 'portuguese', 'romanian', 'russian', 'spanish', 'swedish', 'turkish', 'simple'));
create domain update_date_time as timestamp default now() not null;
