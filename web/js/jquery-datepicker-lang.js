/* English UK initialisation for the jQuery UI date picker plugin. */
/* Written by Stuart. */
(function($) {
        $.datepicker.regional['en'] = {
                clearText: 'Clear', clearStatus: 'Erase the current date',
                closeText: 'Done', closeStatus: 'Close without change',
                prevText: 'Prev', prevStatus: 'Show the previous month',
                prevBigText: '&#x3c;&#x3c;', prevBigStatus: 'Show the previous year',
                nextText: 'Next', nextStatus: 'Show the next month',
                nextBigText: '&#x3e;&#x3e;', nextBigStatus: 'Show the next year',
                currentText: 'Today', currentStatus: 'Show the current month',
                monthNames: ['January','February','March','April','May','June',
                'July','August','September','October','November','December'],
                monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                monthStatus: 'Show a different month', yearStatus: 'Show a different year',
                weekHeader: 'Wk', weekStatus: 'Week of the year',
                dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
                dayStatus: 'Set DD as first week day', dateStatus: 'Select DD, M d',
                dateFormat: 'dd/mm/yy', firstDay: 1,
                initStatus: 'Select a date', isRTL: false,
                showMonthAfterYear: false, yearSuffix: ''};
//         $.datepicker.setDefaults($.datepicker.regional['en']);
})(jQuery);

/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood{at}iinet.com.au) and Stéphane Nahmani (sholby@sholby.net). */
(function($) {
        $.datepicker.regional['fr'] = {
                clearText: 'Effacer', clearStatus: 'Effacer la date sélectionnée',
                closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
                prevText: '&#x3c;Préc', prevStatus: 'Voir le mois précédent',
                prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
                nextText: 'Suiv&#x3e;', nextStatus: 'Voir le mois suivant',
                nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
                currentText: 'Courant', currentStatus: 'Voir le mois courant',
                monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
                'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
                monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
                'Jul','Aoû','Sep','Oct','Nov','Déc'],
                monthStatus: 'Voir un autre mois', yearStatus: 'Voir une autre année',
                weekHeader: 'Sm', weekStatus: '',
                dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
                dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
                dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: '\'Choisir\' le DD d MM',
                dateFormat: 'dd/mm/yy', firstDay: 1,
                initStatus: 'Choisir la date', isRTL: false,
                showMonthAfterYear: false, yearSuffix: ''};
//         $.datepicker.setDefaults($.datepicker.regional['fr']);
})(jQuery);

/* Dutch (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Mathias Bynens <http://mathiasbynens.be/> */
(function($) {
        $.datepicker.regional['nl'] = {
                clearText: 'Wissen', clearStatus: 'Wis de huidige datum',
                closeText: 'Sluiten', closeStatus: 'Sluit zonder verandering',
                prevText: '←', prevStatus: 'Bekijk de vorige maand',
                prevBigText: '«', nextBigStatus: 'Bekijk het vorige jaar',
                nextText: '→', nextStatus: 'Bekijk de volgende maand',
                nextBigText: '»', nextBigStatus: 'Bekijk het volgende jaar',
                currentText: 'Vandaag', currentStatus: 'Bekijk de huidige maand',
                monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
                'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
                monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
                'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
                monthStatus: 'Bekijk een andere maand', yearStatus: 'Bekijk een ander jaar',
                weekHeader: 'Wk', weekStatus: 'Week van het jaar',
                dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
                dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
                dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
                dayStatus: 'Stel DD in als eerste dag van de week', dateStatus: 'dd/mm/yy',
                dateFormat: 'dd/mm/yy', firstDay: 1,
                initStatus: 'Kies een datum', isRTL: false,
                showMonthAfterYear: false, yearSuffix: ''};
        $.datepicker.setDefaults($.datepicker.regional['nl']);
})(jQuery);
