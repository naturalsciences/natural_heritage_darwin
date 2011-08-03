/** Transform some accent string to ascii
ex: é to e
*/
CREATE OR REPLACE FUNCTION to_ascii(bytea, name)
RETURNS text STRICT AS 'to_ascii_encname' LANGUAGE internal;