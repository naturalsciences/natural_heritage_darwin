#/bin/sh
dir=$(dirname $(which $0));
trans_url="http://darwin-trans/message/xliff/part"
orig_url="http://darwin-trans/message/origxliff/part"


if [ "$1" = "orig" ]; then
  wget --no-proxy "$orig_url/2/" -O "$dir/../apps/public/i18n/en/messages.xml"
  wget --no-proxy "$orig_url/1/" -O "$dir/../apps/backend/i18n/en/messages.xml"
fi

#Backend 
wget --no-proxy "$trans_url/1/lang/1" -O "$dir/../apps/backend/i18n/fr/messages.xml"
wget --no-proxy "$trans_url/1/lang/39" -O "$dir/../apps/backend/i18n/nl/messages.xml"

# Public
wget --no-proxy "$trans_url/2/lang/1" -O "$dir/../apps/public/i18n/fr/messages.xml"
wget --no-proxy "$trans_url/2/lang/39" -O "$dir/../apps/public/i18n/nl/messages.xml"

