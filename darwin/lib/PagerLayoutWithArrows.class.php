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

	
		
		$current=(int)$pager->getPage();
		$last=(int)$pager->getLastPage();
		$last=$pager->getLastPage();
		$pager="<input type='text' class='page_shortcut vvsmall_size' value='".$current."'/>/".(string)$last;
		$pager.="<input type='button' class='go_page' value='go'></input><div show='hidden'><a class='hidden_shortcut'/></div>";
		$str .=$pager;
		
		
		$str.="<script language='javascript'>
			$('.go_page').click(
			
				function()
				{					
					var first=$('ul.pager_nav a:first-child');					
					var shortcut_page=$('.page_shortcut').val();					
					if(Math.floor(shortcut_page) == shortcut_page && $.isNumeric(shortcut_page))
					{						
						if(parseInt(shortcut_page)>=1&&parseInt(shortcut_page)<='".$last."')
						{							
							var tmplink_str=$(first[0]).attr('href');
							var tmplink=tmplink_str.split('/');
							tmplink[tmplink.length-1]=shortcut_page;
							$('.hidden_shortcut').attr('href',tmplink.join('/'));
							$('.hidden_shortcut').click();
							
						}
					}
				}
			);
		
		</script>";
        // Possible wish to return value instead of print it on screen
        if ($return) {
            return $str;
        }

        echo $str;
    }
}
