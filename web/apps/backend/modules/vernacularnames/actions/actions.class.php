<?php

/**
 * vernacularnames actions.
 *
 * @package    darwin
 * @subpackage vernacularnames
 * @author     DB team <darwin-ict@naturalsciences.be>
 */
class vernacularnamesActions extends DarwinActions
{
  public function executeVernacularnames(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();

    $this->forward404Unless( $request->hasParameter('id') && $request->hasParameter('table'));

    $this->ref_object = Doctrine::getTable(DarwinTable::getModelForTable($request->getParameter('table')))->find($request->getParameter('id'));
    $this->forward404Unless($this->ref_object);
    $this->form = new  GroupedVernacularNamesForm(null,array('table' => $request->getParameter('table'), 'id' => $request->getParameter('id')));

    if($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('grouped_vernacular'));
      if($this->form->isValid())
      {
        try{
          $this->form->save();
          return $this->renderText('ok');

        }
        catch(Doctrine_Exception $ne)
        {
          $e = new DarwinPgErrorParser($ne);
          $error = new sfValidatorError(new savedValidator(),$e->getMessage());
          $this->form->getErrorSchema()->addError($error);
        }
      }
    }

  }
  
  public function executeAddValue(sfWebRequest $request)
  {
    if(!$this->getUser()->isAtLeast(Users::ENCODER)) $this->forwardToSecureAction();

    $number = intval($request->getParameter('num'));

    $form = new  GroupedVernacularNamesForm(null,array('no_load'=>true));

    $form->addValue($number, $request->getParameter('key'));

    return $this->renderPartial('vernacular_names_values',array('form' => $form['newVal'][$number]));
  }

}
