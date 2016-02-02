<?php

/**
 * Reports form.
 *
 * @package    darwin
 * @subpackage form
 * @author     DB team <darwin-ict@naturalsciences.be>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ReportsForm extends BaseReportsForm
{
  public function configure()
  {
    $default_vals = $this->getOption('default_vals');
    $format = Reports::getFormatFor($this->options['name']) ;
    $widgets_options = Reports::getFieldsOptions($this->options['name']);

    $this->widgetSchema['name'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['name'] = new sfValidatorPass() ;
    $this->setDefault('name', $this->options['name']) ;
    
    $this->widgetSchema['comment'] = new sfWidgetFormInputText(array(), array('maxlength'=>255));
    $this->validatorSchema['comment'] = new sfValidatorPass() ;

    $this->widgetSchema['format'] = new sfWidgetFormChoice(array('choices' => $format));
    $this->validatorSchema['format'] = new sfValidatorChoice(array('choices' => $format)) ;

    /*##########################################################################
     *  Annual reports fields
     *##########################################################################
     */

    /*
     * Date fields
     */

    $yearsKeyVal = range(intval(sfConfig::get('dw_yearRangeMin')), intval(sfConfig::get('dw_yearRangeMax')));
    $years = array_combine($yearsKeyVal, $yearsKeyVal);
    $dateText = array('year'=>'yyyy', 'month'=>'mm', 'day'=>'dd');
    $minDate = new FuzzyDateTime(strval(min($yearsKeyVal).'/01/01'));
    $maxDate = new FuzzyDateTime(strval(max($yearsKeyVal).'/12/31'));
    $dateLowerBound = new FuzzyDateTime(sfConfig::get('dw_dateLowerBound'));
    $dateUpperBound = new FuzzyDateTime(sfConfig::get('dw_dateUpperBound'));
    $maxDate->setStart(false);

    /*
     *  Collections selection
     */

    $this->widgetSchema['collection_ref'] = new widgetFormCompleteButtonRef(array(
      'model' => 'Collections',
      'link_url' => 'collection/choose',
      'method' => 'getName',
      'box_title' => $this->getI18N()->__('Choose Collection'),
      'button_class'=>'check_right',
      'complete_url' => 'catalogue/completeName?table=collections',
    ));
    $this->widgetSchema->setLabels(array('collection_ref' => 'Collection')) ;
    $this->validatorSchema['collection_ref'] = new sfValidatorInteger(array('required'=>true));

    /*
     * Fuzzy date from
     */

    $this->widgetSchema['date_from'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );
    $this->validatorSchema['date_from'] = new fuzzyDateValidator(
      array(
        'required' => true,                       
        'from_date' => true,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateLowerBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    /*
     * Fuzzy date to
     */

    $this->widgetSchema['date_to'] = new widgetFormJQueryFuzzyDate(array(
      'culture'=>$this->getCurrentCulture(),
      'image'=>'/images/calendar.gif',
      'format' => '%day%/%month%/%year%',
      'years' => $years,
      'empty_values' => $dateText,
      ),
      array('class' => 'to_date')
    );
 
    $this->validatorSchema['date_to'] = new fuzzyDateValidator(
      array(
        'required' => true,                       
        'from_date' => false,                       
        'min' => $minDate,                         
        'max' => $maxDate,
        'empty_value' => $dateUpperBound,
      ),
      array('invalid' => 'Date provided is not valid')
    );

    /*##########################################################################
     * Catalogues listing fields
     *##########################################################################
     */

    /*
     * Type of catalogue targeted
     */
    if (isset($widgets_options['catalogue_type'])) {
      $this->widgetSchema[ 'catalogue_type' ] = new sfWidgetFormChoice(array(
                                                                             'choices' => $widgets_options[ 'catalogue_type' ]['values'],
                                                                             'default' => $widgets_options[ 'catalogue_type' ]['default_value']
                                                                            )
                                                                       );
      $this->validatorSchema[ 'catalogue_type' ] = new sfValidatorChoice(array ('choices' => array_keys($widgets_options[ 'catalogue_type' ]['values'])));
      $form_name = $this->getName();
      $attached_to_id = $form_name . '_catalogue_type';
    }

    /*
     * Number of Records to get from the report
     */
    if (isset($widgets_options['nbr_records'])) {
      $this->widgetSchema[ 'nbr_records' ] = new sfWidgetFormChoice(
        array (
          'choices' => $widgets_options[ 'nbr_records' ][ 'values' ],
          'default' => $widgets_options[ 'nbr_records' ][ 'default_value' ]
        )
      );
      $this->validatorSchema[ 'nbr_records' ] = new sfValidatorChoice(array ('choices' => array_keys($widgets_options[ 'nbr_records' ][ 'values' ])));
    }

    /*
     * Catalogue unit ref
     */

    if (isset($widgets_options['catalogue_unit_ref'])) {
      $model = strtolower($this->getOption('model_name', 'taxonomy'));
      if ($model == 'zoology' || $model == 'botany') {
        $model = 'taxonomy';
      }
      $modelUC = ucfirst($model);

      $this->widgetSchema[ 'catalogue_unit_ref' ] = new widgetFormButtonRefMultiple(
        array (
          'model' => $modelUC,
          'method' => 'getFormatedName',
          'link_url' => $model . '/choose?with_js=1',
          'box_title' => $this->getI18N()->__('Choose Yourself'),
          'label' => $this->getI18N()->__('Catalogue unit'),
          'partial_url' => 'catalogue/renderTableRowForButtonRefMultiple',
          'partial_controler' => 'catalogue',
          'partial_action' => 'renderTableRowForButtonRefMultiple',
          'on_change_attached_to_id' => (isset($attached_to_id)) ? $attached_to_id : NULL,
          'on_change_url_for_widget_renew' => 'report/getReport',
          'on_change_url_for_widget_renew_params' => $this->getOption('name'),
        ),
        array ('class' => 'ref_multiple_ids',)
      );
      $this->validatorSchema[ 'catalogue_unit_ref' ] = new sfValidatorString(array ('required' => TRUE));
      $this->validatorSchema[ 'catalogue_unit_ref' ]->setMessage('required', 'You need to provide at least one catalogue unit');
      $this->mergePostValidator(new buttonRefMultipleValidatorSchema());
    }

    /*##########################################################################
     * Loan forms fields
     *##########################################################################
     */

    /*
     * Loan ID
     */

    if($this->getOption( 'with_js', false ) === true && !empty($default_vals['loan_id'])) {
      $this->widgetSchema[ 'loan_id' ] = new sfWidgetFormInputHidden(array('default'=>$default_vals['loan_id']));
    }
    else {
      $this->widgetSchema[ 'loan_id' ] = new widgetFormCompleteButtonRef(
        array (
          'model' => 'Loans',
          'link_url' => 'loan/choose',
          'method' => 'getName',
          'box_title' => $this->getI18N()->__('Choose Loan'),
          'button_class' => 'check_right',
          'complete_url' => 'catalogue/completeName?table=loans',
        )
      );
    }
    $this->validatorSchema[ 'loan_id' ] = new sfValidatorInteger(array ('required' => TRUE));

    /*
     * Loan Target Selected
     */
    if (isset($widgets_options['loan_target_selected'])) {
      $this->widgetSchema[ 'loan_target_selected' ] = new sfWidgetFormChoice(
        array (
          'choices' => $widgets_options[ 'loan_target_selected' ][ 'values' ],
          'default' => $widgets_options[ 'loan_target_selected' ][ 'default_value' ],
          'multiple' => true,
          'expanded' => false
        )
      );
      $this->validatorSchema[ 'loan_target_selected' ] = new sfValidatorChoice(array (
                                                                                 'choices' => array_keys($widgets_options[ 'loan_target_selected' ][ 'values' ]),
                                                                                 'multiple' => true
                                                                               ));
    }

    /*
     * Loan Target Catalogues
     */
    if (isset($widgets_options['loan_target_catalogues'])) {
      $this->widgetSchema[ 'loan_target_catalogues' ] = new sfWidgetFormChoice(
        array (
          'choices' => $widgets_options[ 'loan_target_catalogues' ][ 'values' ],
          'default' => $widgets_options[ 'loan_target_catalogues' ][ 'default_value' ],
          'multiple' => true,
          'expanded' => false
        )
      );
      $this->validatorSchema[ 'loan_target_catalogues' ] = new sfValidatorChoice(array (
                                                                                   'choices' => array_keys($widgets_options[ 'loan_target_catalogues' ][ 'values' ]),
                                                                                   'multiple' => true
                                                                                 ));
    }

    /*##########################################################################
     * Encoders statistics forms fields
     *##########################################################################
     */

    if ( isset($widgets_options['users_array']) && $this->getOption('current_user', null) !== null ) {
      $this->widgetSchema[ 'users_array' ] = new sfWidgetFormDarwinDoctrineChoice(
        array(
          'model' => 'Users',
          'table_method' => array(
                                  'method'=>'getRestrictedEncodersList',
                                  'parameters'=>array(
                                    $this->getOption('current_user')
                                  )
                                 ),
          'multiple' => true,
          'default' => 0
        )
      );
      $choices = $this->widgetSchema[ 'users_array' ]->getChoices();
      $this->validatorSchema[ 'users_array' ] = new sfValidatorChoice(
        array(
          'choices' => array_keys($choices),
          'multiple' => true
        )
      );
    }

    /*##########################################################################
     * Multiple use cases forms fields
     *##########################################################################
     */

    /*
     * Language
     */
    if (isset($widgets_options['lang'])) {
      $this->widgetSchema[ 'lang' ] = new sfWidgetFormChoice(
        array (
          'choices' => $widgets_options[ 'lang' ][ 'values' ],
          'default' => $widgets_options[ 'lang' ][ 'default_value' ]
        )
      );
      $this->validatorSchema[ 'lang' ] = new sfValidatorChoice(array (
                                                                 'choices' => array_keys($widgets_options[ 'lang' ][ 'values' ])
                                                               ));
    }

    /*##########################################################################
     * Definition of list of fields to be used
     *##########################################################################
     */
    $this->useFields(array_merge(array('name','format','comment'),array_keys($this->options['fields']))) ;
  }
}
