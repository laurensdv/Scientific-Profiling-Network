<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SMNTagFilter
 *
 * @author laurens
 */
class SMNTagFilter {
    protected function just_clean($string) {
    // Replace other special chars
        $specialCharacters = array(
            '#' => '',
            '$' => '',
            '%' => '',
            '&' => '',
            '@' => '',
            '.' => '',
            '�' => '',
            '+' => '',
            '=' => '',
            '�' => '',
        );

        while (list($character, $replacement) = each($specialCharacters)) {
            $string = str_replace($character, '-' . $replacement . '-', $string);
        }

        $string = strtr($string,
                        "������? ����������������������������������������������",
                        "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"
        );

        // Remove all remaining other unknown characters
        $string = preg_replace('/[^a-zA-Z0-9-]/', ' ', $string);
        $string = preg_replace('/^[-]+/', '', $string);
        $string = preg_replace('/[-]+$/', '', $string);
        $string = preg_replace('/[-]{2,}/', ' ', $string);

        return $string;
    }

    protected function replace_accents($string) {
        return str_replace(array('à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ', 'À','Á','Â','Ã','Ä', 'Ç', 'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'), array('a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y', 'A','A','A','A','A', 'C', 'E','E','E','E', 'I','I','I','I', 'N', 'O','O','O','O','O', 'U','U','U','U', 'Y'), $string);
    }

    public function syntacticFilter($tags) {
        foreach ($tags as $key => $tag) {
            if (strlen($tag) == 1)
                unset($tags[$key]);
            else
            $tags[$key] = PorterStemmer::Stem($tag);
            $tags[$key] = SMNTagFilter::replace_accents($tags[$key]);
            $tags[$key] = SMNTagFilter::just_clean($tags[$key]);
            $tags[$key] = iconv("UTF-8", "ASCII//TRANSLIT", $tags[$key]);
        }
        return $tags;
    }

    public function misSpellFilter($tags) {
        $DYM = new DYM();
        $urls = array();
        foreach ($tags as $key => $tag) {
            //if($DYM->search($tag)) $tags[$key]=$DYM->correct;
            $urls[$tag] = $DYM->buildURL($tag);
        }
        $results = $DYM->searchMulti($urls);
        return $results;
    }

    public function wordNetFilter($tags) {
        //replace 2 similar tags by most frequent used sense synonyms
        return $tags;
    }

    public function filter($tags) {
        $step1 = SMNTagFilter::syntacticFilter($tags);

        if (DOSPELLCHECK) {
            $corrected_tags = SMNTagFilter::misSpellFilter($step1);
            if(is_array($corrected_tags)&&$corrected_tags!=null)
                foreach ($corrected_tags as $or => $corrected) {
                  $key = array_search($or,$step1);
                  $step1[$key] =$corrected;
                }
        }
        
        $step2 = $step1;

        if (DOWORDNET)
            $step3 = SMNTagFilter::wordNetFilter($step2);
        else
            $step3 = $step2;

        $finalstep = $step2;

        return $finalstep;
    }

    public function interestFilter($tags) {
        //TODO optimize filter
        $result = SMNTagFilter::filter($tags);
        $interests = array();
        foreach($result as $key => $tag) {
            if(!SMNConference::isConference($tag) && !SMNTagQueries::isEntity($tag)) {
                $interests[$key] = $tag;
            }
        }
        return $interests;
    }

    public function eventFilter($tags) {
        $result = SMNTagFilter::filter($tags);
        $conferences = array();
        foreach($result as $key => $tag) {
            //Conferences
            if(SMNConference::isConference($tag)) {
                        $conferencedata = SMNConference::findConference($tag);
                        $conferencename = $conferencedata['name'];
                        $conferences[$tag] = $conferencename;
                   }
            }
            return $conferences;
    }
}

?>
