<?php
class PagerLayoutWithArrows extends Doctrine_Pager_Layout
{
    public function display($options = array(), $return = false)
    {
        $pager = $this->getPager();
        $str = '';

        // First page
        $this->addMaskReplacement('page', '<span class="nav_arrow">&laquo;</span>', true);
        $options['page_number'] = $pager->getFirstPage();
        $str .= $this->processPage($options);

        // Previous page
        $this->addMaskReplacement('page', '<span class="nav_arrow">&lsaquo;</span>', true);
        $options['page_number'] = $pager->getPreviousPage();
        $str .= $this->processPage($options);

        // Pages listing
        $this->removeMaskReplacement('page');
        $this->setSelectedTemplate('<li class="page_selected">[{%page}]</li>');
        $str .= parent::display($options, true);
        $this->setSelectedTemplate('<li>{%page}</li>');

        // Next page
        $this->addMaskReplacement('page', '<span class="nav_arrow">&rsaquo;</span>', true);
        $options['page_number'] = $pager->getNextPage();
        $str .= $this->processPage($options);

        // Last page
        $this->addMaskReplacement('page', '<span class="nav_arrow">&raquo;</span>', true);
        $options['page_number'] = $pager->getLastPage();
        $str .= $this->processPage($options);

        // Possible wish to return value instead of print it on screen
        if ($return) {
            return $str;
        }

        echo $str;
    }
}
