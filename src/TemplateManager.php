<?php

class TemplateManager
{
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }
	
	/**
	 * TODO : unit test deprecated
	 * Replace the content by data
	 *
	 * @param       $text
	 * @param array $data
	 *
	 * @return mixed
	 */
    private function computeText($text, array $data)
    {
	    // Check if the key 'quote' on data is and instance of Quote
        if ($data['quote'] instanceof Quote) {
        	$quote = $data['quote'];
        	
            $_quote       = QuoteRepository::getInstance()->getById($quote->id); // get quote repository by quote ID
            $_site        = SiteRepository::getInstance()->getById($quote->siteId); // get site repository by quote site ID
            $_destination = DestinationRepository::getInstance()->getById($quote->destinationId);// get destination repository by destination ID
            
            $replaceDestinationLink = $_site->url . '/' . $_destination->countryName . '/quote/' . $_quote->id;
            
	        $text = str_replace( '[quote:destination_link]', $replaceDestinationLink, $text); // replace [quote:destination_link] if exist by the string $replaceDestinationLink
            $text = str_replace( '[quote:summary_html]', Quote::renderHtml($_quote), $text);// replace [quote:summary_html] if exist by the render html
            $text = str_replace( '[quote:summary]', Quote::renderText($_quote), $text );// replace [quote:summary] if exist by the summary
            $text = str_replace('[quote:destination_name]', $_destination->countryName, $text);// replace [quote:destination_name] if exist by destination name
        }
        
		// check if the key 'user' is and instance of User then take it or get the current user
        $_user = ($data['user'] instanceof User) ? $data['user']  : ApplicationContext::getInstance()->getCurrentUser();
        
        if($_user) {
        	$text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text); // replace [user:first_name] if exist by the firstname of the current user
        }

        return $text;
    }
}
