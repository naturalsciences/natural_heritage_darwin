<?php 
include(dirname(__FILE__).'/../bootstrap/unit.php');
$t = new lime_test(81, new lime_output_color());

$t->info('FuzzyDateTime instanciation');
$t->isa_ok(new FuzzyDateTime(), 'FuzzyDateTime', 'New creates an object of the right class');
$t->can_ok(new FuzzyDateTime(), 'validateDateYearLength', 'Object FuzzyDateTime has a method named: validateDateYearLength');
$t->can_ok(new FuzzyDateTime(), 'validateDateOtherPartLength', 'Object FuzzyDateTime has a method named: validateDateOtherPartLength');
$t->can_ok(new FuzzyDateTime(), 'getDateTimeStringFromArray', 'Object FuzzyDateTime has a method named: getDateTimeStringFromArray');
$t->can_ok(new FuzzyDateTime(), 'checkDateArray', 'Object FuzzyDateTime has a method named: checkDateArray');
$t->can_ok(new FuzzyDateTime(), 'setMask', 'Object FuzzyDateTime has a method named: setMask');
$t->can_ok(new FuzzyDateTime(), 'setStart', 'Object FuzzyDateTime has a method named: setStart');
$t->can_ok(new FuzzyDateTime(), 'setWithTime', 'Object FuzzyDateTime has a method named: setWithTime');
$t->can_ok(new FuzzyDateTime(), 'setdateFormat', 'Object FuzzyDateTime has a method named: setDateFormat');
$t->can_ok(new FuzzyDateTime(), 'setTimeFormat', 'Object FuzzyDateTime has a method named: setTimeFormat');
$t->can_ok(new FuzzyDateTime(), 'getMask', 'Object FuzzyDateTime has a method named: getMask');
$t->can_ok(new FuzzyDateTime(), 'getStart', 'Object FuzzyDateTime has a method named: getStart');
$t->can_ok(new FuzzyDateTime(), 'getWithTime', 'Object FuzzyDateTime has a method named: getWithTime');
$t->can_ok(new FuzzyDateTime(), 'getDateFormat', 'Object FuzzyDateTime has a method named: getDateFormat');
$t->can_ok(new FuzzyDateTime(), 'getTimeFormat', 'Object FuzzyDateTime has a method named: getTimeFormat');
$t->can_ok(new FuzzyDateTime(), 'setMaskFromDate', 'Object FuzzyDateTime has a method named: setMaskFromDate');
$t->can_ok(new FuzzyDateTime(), 'getMaskFromDate', 'Object FuzzyDateTime has a method named: getMaskFromDate');
$t->can_ok(new FuzzyDateTime(), 'getMaskFor', 'Object FuzzyDateTime has a method named: getMaskFor');
$t->can_ok(new FuzzyDateTime(), 'getDateTime', 'Object FuzzyDateTime has a method named: getDateTime');
$t->can_ok(new FuzzyDateTime(), 'getDateTimeAsArray', 'Object FuzzyDateTime has a method named: getDateTimeAsArray');
$t->can_ok(new FuzzyDateTime(), 'getDateMasked', 'Object FuzzyDateTime has a method named: getDateMasked');
$t->can_ok(new FuzzyDateTime(), '__ToString', 'Object FuzzyDateTime has a method named: __ToString');
$fdt = new FuzzyDateTime();
$currentDate = $fdt->format('d/m/Y');
$currentDateTime = $fdt->format('d/m/Y H:i:s');
$t->is($fdt->getDateTime(), $currentDate,'Get the date coming with the FuzzyDateTime object instanciation');
$t->is($fdt->getDateTime(true), $currentDateTime,'Get the date and time coming with the FuzzyDateTime object instanciation: ok');
$fdt->setStart(false);
$t->is($fdt->getStart(), false, 'The start value is False');
$fdt->setMask(32);
$t->is($fdt->getMask(), 32, 'The Mask value is 32 (year)');
$t->is(FuzzyDateTime::validateDateYearLength(1975), true, 'The year lenght is ok');
$t->is(FuzzyDateTime::validateDateYearLength(10200), false, 'The year lenght is not ok');
$t->is(FuzzyDateTime::validateDateOtherPartLength(12), true, 'The other dates part lenght is ok');
$t->is(FuzzyDateTime::validateDateOtherPartLength(121), false, 'The other dates part lenght is not ok');
$fdt = new FuzzyDateTime('1975/02/24 13:12:11');
$testArray = array('year'=>'1975', 'month'=>'02', 'day'=>'24', 'hour'=>'13', 'minute'=>'12', 'second'=>'11');
$dateTimeArray = $fdt->getDateTimeAsArray();
$testDate = '24/02/1975';
$testDateTime = '24/02/1975 13:12:11';
$testDateTime2 = '1975/02/24 13:12:11';
$testDateUS = '02/24/1975';
foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
{
  $t->is($dateTimeArray[$key], $testArray[$key], 'Value of '.$key.' is ok: '.$testArray[$key]);
}
$fdt->setWithTime(true);
$t->is($fdt->getDateTime($fdt->getWithTime()), $testDateTime,'Get the date and time coming with the FuzzyDateTime object instanciation: ok');
$t->is($fdt.' as string', $testDateTime.' as string', 'String conversion of FuzzyDateTime object is correct');
$fdt->setDateFormat('m/d/Y');
$t->is($fdt->getDateTime(false), $testDateUS, 'New US date formating correctly applied');
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Full date: Extraction of date from array succeeded !');
$testArray['second']='';
$testDateTime2 = '1975/02/24 13:12:59';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Date without seconds - start flag set at false: Extraction of date from array succeeded !');
$testArray['minute']='';
$testDateTime2 = '1975/02/24 13:59:59';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Date without minutes - start flag set at false: Extraction of date from array succeeded !');
$testArray['hour']='';
$testDateTime2 = '1975/02/24 23:59:59';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Date without hours - start flag set at false: Extraction of date from array succeeded !');
$testArray['day']='';
$testDateTime2 = '1975/02/28 23:59:59';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Date without days - start flag set at false: Extraction of date from array succeeded !');
$testArray['month']='';
$testDateTime2 = '1975/12/31 23:59:59';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Date without months - start flag set at false: Extraction of date from array succeeded !');
$testArray['year']='';
$testDateTime2 = '2038/12/31 23:59:59';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray,false,$fdt->getWithTime()), $testDateTime2, 'Date without years - start flag set at false: Extraction of date from array succeeded !');
$fdt->setStart(true);
$testArray['year']='1975';
$testDateTime2 = '1975/01/01 00:00:00';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart(), $fdt->getWithTime()), $testDateTime2, 'Date without month - start flag set at true: Extraction of date from array succeeded !');
$testArray['month']='02';
$testDateTime2 = '1975/02/01 00:00:00';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart(), $fdt->getWithTime()), $testDateTime2, 'Date without days - start flag set at false: Extraction of date from array succeeded !');
$testArray['day']='24';
$testDateTime2 = '1975/02/24 00:00:00';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart(), $fdt->getWithTime()), $testDateTime2, 'Date without hours - start flag set at false: Extraction of date from array succeeded !');
$testArray['hour']='13';
$testDateTime2 = '1975/02/24 13:00:00';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart(), $fdt->getWithTime()), $testDateTime2, 'Date without minutes - start flag set at false: Extraction of date from array succeeded !');
$testArray['minute']='12';
$testDateTime2 = '1975/02/24 13:12:00';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart(), $fdt->getWithTime()), $testDateTime2, 'Date without seconds - start flag set at false: Extraction of date from array succeeded !');
$testArray['second']='11';
$testDateTime2 = '1975/02/24 13:12:11';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart(), $fdt->getWithTime()), $testDateTime2, 'Date with everything - start flag set at false: Extraction of date from array succeeded !');
$testArray['day']='';
$testDateTime2 = '1975/02/01';
$t->is(FuzzyDateTime::getDateTimeStringFromArray($testArray, $fdt->getStart()), $testDateTime2, 'Date without hours - start flag set at false: Extraction of date from array succeeded !');
$testArray['day']='24';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 63, 'The Mask returned by getMaskFromDate is correct !');
$fdt->setMaskFromDate($testArray);
$t->is($fdt->getMask(), 63, 'Setting of mask from date array worked !');
$testArray['second']='';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 62, 'The Mask returned by getMaskFromDate is correct !');
$testArray['minute']='';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 60, 'The Mask returned by getMaskFromDate is correct !');
$testArray['hour']='';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 56, 'The Mask returned by getMaskFromDate is correct !');
$testArray['day']='';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 48, 'The Mask returned by getMaskFromDate is correct !');
$testArray['month']='';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 32, 'The Mask returned by getMaskFromDate is correct !');
$testArray['year']='';
$t->is(FuzzyDateTime::getMaskFromDate($testArray), 0, 'The Mask returned by getMaskFromDate is correct !');
$fdt->setDateFormat('d/m/Y');
$fdt->setTimeFormat('H:i:s');
$fdt->setWithTime(true);
$fdt->setMask(0);
$t->is($fdt->getDateMasked(), '<em>24/02/1975 13:12:11</em>', 'The date displayed is well: <em>24/02/1975 13:12:11</em>');
$fdt->setMask(1);
$t->is($fdt->getDateMasked(), '<em>24/02/1975 13:12:11</em>', 'The date displayed is well: <em>24/02/1975 13:12:11</em>');
$fdt->setMask(2);
$t->is($fdt->getDateMasked(), '<em>24/02/1975 13:12:11</em>', 'The date displayed is well: <em>24/02/1975 13:12:11</em>');
$fdt->setMask(4);
$t->is($fdt->getDateMasked(), '<em>24/02/1975 13:12:11</em>', 'The date displayed is well: <em>24/02/1975 13:12:11</em>');
$fdt->setMask(8);
$t->is($fdt->getDateMasked(), '<em>24/02/1975 13:12:11</em>', 'The date displayed is well: <em>24/02/1975 13:12:11</em>');
$fdt->setMask(16);
$t->is($fdt->getDateMasked(), '<em>24/02/1975 13:12:11</em>', 'The date displayed is well: <em>24/02/1975 13:12:11</em>');
$fdt->setMask(32);
$t->is($fdt->getDateMasked(), '<em>24/02/</em>1975<em> 13:12:11</em>', 'The date displayed is well: <em>24/02/</em>1975<em> 13:12:11</em>');
$fdt->setMask(33);
$t->is($fdt->getDateMasked(), '<em>24/02/</em>1975<em> 13:12:11</em>', 'The date displayed is well: <em>24/02/</em>1975<em> 13:12:11</em>');
$fdt->setMask(48);
$t->is($fdt->getDateMasked(), '<em>24/</em>02/1975<em> 13:12:11</em>', 'The date displayed is well: <em>24/</em>02/1975<em> 13:12:11</em>');
$fdt->setMask(56);
$t->is($fdt->getDateMasked(), '24/02/1975<em> 13:12:11</em>', 'The date displayed is well: 24/02/1975<em> 13:12:11</em>');
$fdt->setWithTime(false);
$fdt->setMask(48);
$t->is($fdt->getDateMasked(), '<em>24/</em>02/1975', 'The date displayed is well: <em>24/</em>02/1975');
$fdt->setWithTime(true);
$fdt->setMask(60);
$t->is($fdt->getDateMasked(), '24/02/1975 13<em>:12:11</em>', 'The date displayed is well: 24/02/1975 13<em>:12:11</em>');
$fdt->setMask(62);
$t->is($fdt->getDateMasked(), '24/02/1975 13:12<em>:11</em>', 'The date displayed is well: 24/02/1975 13:12<em>:11</em>');
$fdt->setMask(63);
$t->is($fdt->getDateMasked(), '24/02/1975 13:12:11', 'The date displayed is well: 24/02/1975 13:12:11');
$t->is(FuzzyDateTime::checkDateTimeStructure($testArray), '', 'Date structure test ok');
$testArray['year']='1975';
$t->is(FuzzyDateTime::checkDateTimeStructure($testArray), '', 'Date structure test ok');
$testArray['day']='24';
$t->is(FuzzyDateTime::checkDateTimeStructure($testArray), 'month_missing', 'Date structure test ok');
$testArray['year']='';
$t->is(FuzzyDateTime::checkDateTimeStructure($testArray), 'year_missing', 'Date structure test ok');
$testArray['year']='1975';
$testArray['month']='02';
$testArray['day']='';
$testArray['hour']='02';
$t->is(FuzzyDateTime::checkDateTimeStructure($testArray), 'time_without_date', 'Date structure test ok');
$testArray['year']='0001';
$testArray['month']='01';
$testArray['day']='01';
$fdt=new FuzzyDateTime($testArray);
$t->is($fdt->format('d/m/Y'), '01/01/0001', 'Date lower bound format ok');
