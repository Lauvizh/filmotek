<?php

/*--------------------------- CONNECTION A LA BASE -----------------------*/
function dbConnection(){
    global $CONFIG;
    try{
        $db = new PDO($CONFIG['dsn'],$CONFIG['dbUser'],$CONFIG['dbPassword']);
        $db->exec('SET CHARACTER SET UTF8');
    }catch(PDOException $e){
        if (strstr($e->getMessage(),'Unknown database')){
            die('<h1>Houston we have a problem !</h1><em>Contact your administrator</em>');
        }
        else{
            die('<h1>Oups!</h1><em>'.$e->getMessage().'</em>');
        }
        
    }
    return $db;
}
/*--------------------------- VERIFY IF TABLES EXIST----------------------*/
function tableExists($id){
    global $db;
    global $CONFIG;

    $r = $db->query('SELECT COUNT(*) FROM '.$id);
    if(!$r) {
        return false;
    }else{
        return true;
    }
}
/*--------------------------- VERIF EMAIL -----------------------*/
function isEmail($adresse){
    if (filter_var($adresse, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

/*--------------------- INITIALISE FILMOTEK -------------------------------*/
function initializeFilmotek($e, $s, $pw){
    global $db;
    global $CONFIG;

    $U = $CONFIG['PrefixDB'].'Users'; //determine le nom de base des utilisateurs
    $UC = $CONFIG['PrefixDB'].'UsersConnect'; //determine le nom de base de connection des utilisateurs
    $L = $CONFIG['PrefixDB'].'List'; //Movie List

    //request to create the users table
    $creatable['U'] = 'CREATE TABLE IF NOT EXISTS '.$U.' (
                      `id` tinyint(3) NOT NULL AUTO_INCREMENT,
                      `rang` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT "user",
                      `surname` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
                      `passhash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1';

    //request to create the users connection table
    $creatable['UC'] = 'CREATE TABLE IF NOT EXISTS '.$UC.' (
                      `id` int(16) NOT NULL AUTO_INCREMENT,
                      `user` tinyint(3) NOT NULL,
                      `date_co` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1';

    //request to create the movie List table
    $creatable['L'] = 'CREATE TABLE IF NOT EXISTS '.$L.' (
                      `id` mediumint(20) NOT NULL AUTO_INCREMENT,
                      `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      `title_orig` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      `category` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
                      `genre` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
                      `country` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      `year` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
                      `duration` smallint(11) NOT NULL,
                      `description` varchar(2028) COLLATE utf8_unicode_ci DEFAULT NULL,
                      `actors` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
                      `directors` varchar(256) COLLATE utf8_unicode_ci DEFAULT NULL,
                      `media_location` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
                      `media_language` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
                      `date_add` date NOT NULL,
                      `date_update` datetime NOT NULL,
                      `trailer_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                      PRIMARY KEY (`id`),
                      KEY `title` (`title`)
                    ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1';


    foreach ($creatable as $SQLreq) {
        $c = $db->exec($SQLreq);
    }

    $passhash = password_hash($pw,PASSWORD_BCRYPT);

    $addadmin = $db->prepare('INSERT INTO '.$U.' (`rang`, `surname`, `passhash`, `email`, `date_inscription`) VALUES (:rang, :surname, :passhash, :email, NOW())');
    $addadmin->execute(array(
        'rang' => 'admin',
        'surname' => (string)$s,
        'passhash' => $passhash,
        'email' => $e,
        ));

    if ($addadmin) {
        return true;
    }
    else{
        return false;
    }
}
/*--------------------- USER CONNECTION -------------------------------*/
function UserConnect($e, $p){
    global $db;
    global $CONFIG;

    $U = $CONFIG['PrefixDB'].'Users'; //determine le nom de base des utilisateurs
    $UC = $CONFIG['PrefixDB'].'UsersConnect'; //determine le nom de base de connection des utilisateurs

    // requette dans la base pour verifier que l'utilisateur existe 
    $req_U = $db->prepare("SELECT id,email,surname,rang,passhash FROM ".$U." WHERE email = :email");
    $req_U->execute(array('email' => $e));
    $ResultU = $req_U->fetch(PDO::FETCH_ASSOC);

    usleep(500000); // ralentis le processus de control pour eviter les piratages sur formulaire.

    if(empty($ResultU)){
        return false;
    }
    else{
        if (password_verify($p, $ResultU['passhash'])) {
            $id = $ResultU['id'];
            $req_UC = $db->exec("INSERT INTO $UC (user,date_co) VALUES($id,NOW())");
                //état de session true
            unset($ResultU['passhash']);
            return $ResultU;
        }
        else {
            return false;
        }
    }

}

/*--------------------------- GET GENRES -----------------------*/

function getList($field){
    global $db;
    global $CONFIG;
    $List = array();
    //requette
        $r = $db->query('SELECT '.$field.' FROM '.$CONFIG['PrefixDB'].'List')->fetchall(PDO::FETCH_ASSOC);

        if(empty($r)){
            throw new Exception('No '.$field.' found.');
        }
        else{
            foreach ($r as $items) {
               $temp_items = preg_split("/[,\-\/.]+/", $items[$field]);
               //array_merge($List,$temp_items);
                $i=0;
               foreach ($temp_items as $key => $value) {
                    $value = trim($value);
                    $value = ucfirst(strtolower($value));
                    $urlvalue = ValideUrl($value);
                    if (!isset($List[$urlvalue])) {
                        $List[$urlvalue] = $value;
                    }
                    else{
                        $List[$urlvalue.$i] = $value;
                        $i++;
                    }
                    
               }
            }
            $List = array_unique($List,SORT_STRING);
            natcasesort($List);
        }
        return $List;
    }

/*--------------------------- GET LAST ADDED -----------------------*/

function getLastAdd($category){
    global $db;
    global $CONFIG;
    //requette
    if (!is_string($category)) {
        throw new Exception('wrong type category.');
        }
    else{
        $r = $db->query('SELECT id,title,date_add FROM '.$CONFIG['PrefixDB'].'List WHERE category = "'.$category.'" ORDER BY date_add DESC, title DESC LIMIT '. $CONFIG['homepage_movies_by_category'])->fetchall(PDO::FETCH_ASSOC);

        if(empty($r)){
            throw new Exception('No movie found.');
        }
        else{
            return $r;
        }  
    }
}

/*--------------------------- GET MOVIES BY GENRE -----------------------*/

function getMoviesByGenre($genre){
    global $db;
    global $CONFIG;
    //requette
    if (!is_string($genre)) {
        throw new Exception('wrong type of genre.');
        }
    else{

        $genreSSaccent = htmlentities($genre, ENT_NOQUOTES, 'utf-8');
        $genreSSaccent = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $genreSSaccent);
        $genreSSaccent = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $genreSSaccent); // pour les ligatures e.g. '&oelig;'
        $genreSSaccent = preg_replace('#&[^;]+;#', '', $genreSSaccent); // supprime les autres caractères

        $r = $db->query('SELECT id,title,genre FROM '.$CONFIG['PrefixDB'].'List WHERE genre REGEXP "([[:blank:]]?)'.$genre.'(([[:punct:]]+|$)|[[:blank:]]+[[:punct:]]+)" ORDER BY date_add DESC, title DESC')->fetchall(PDO::FETCH_ASSOC);

        if(empty($r)){
            throw new Exception('No movie found.');
        }
        else{
            return $r;
        }  
    }
}

/*--------------------------- GET MOVIE DETAIL -----------------------*/
function getMovieInfos($id){
    global $db;
    global $CONFIG;
    //requette
    if (!is_integer((int)$id)) {
        throw new Exception('wrong type id.');
    }
    else{
        $r = $db->query('SELECT * FROM '.$CONFIG['PrefixDB'].'List WHERE id='.$id)->fetch(PDO::FETCH_ASSOC);
        if(empty($r)){
            throw new Exception('No movie found.');
        }
        else{
            if (!empty($r['actors'])) {
                $actors_temp = explode(',',$r['actors']);
                foreach ($actors_temp as $k => $actor) {
                    $actors[ValideUrl(trim($actor))] = trim($actor);
                }
                $r['actors'] = $actors;
            }
            if (!empty($r['directors'])) {
                $directors_temp = explode(',',$r['directors']);
                foreach ($directors_temp as $k => $director) {
                    $directors[ValideUrl(trim($director))] = trim($director);
                }
                $r['directors'] = $directors;
            }
            return $r;
        }        
    }
}

/*--------------------------- GET SEARCH RESULTS -----------------------*/
function getSearchResults($request,$mode = 'global', $infofields = array('title','title_orig','actors','directors','year')){
    global $db;
    global $CONFIG;
    $results = array();
    $temp_results = array();
    //requette

    if ($mode =='global') {
        $key_words = explode(" ",$request);
    }
    else{
        $key_words[] = htmlspecialchars($request);
    }

    $nbkey_words = count($key_words);

    // search for movies how containe a keyword
    foreach ($key_words as $key_word) {
        // key word need to be longer than 2 char to avoid a, an, etc... and all the shorts articles
        if (strlen($key_word) >= 3) {
            // search in all predeterminate fields.
            foreach ($infofields as $field) {
                $movies = $db->query('SELECT id,title,title_orig,actors,directors,year FROM '.$CONFIG['PrefixDB'].'List WHERE '.$field.' REGEXP "([[:<:]])'.$key_word.'([[:>:]])" ORDER BY year DESC, title ASC')->fetchall(PDO::FETCH_ASSOC);
                if (!empty($movies)) {
                    foreach ($movies as $movie) {
                        // Add the corresponding keyword and field to the info of each movie
                        if (isset($results[$movie['id']])) {
                            $results[$movie['id']]['search'][$field][] = $key_word;
                        }
                        else{
                            $movie['search'][$field][] = $key_word;
                            $results[$movie['id']] = $movie;
                        }
                    }
                }    
            }
        }
    }

    // Sort by number of accurate key words
        foreach ($results as $id => $movie) {
            $nbkword=array();
            foreach ($movie['search'] as $field) {
                foreach ($field as  $keyword) {
                    $nbkword[] = $keyword;
                    }     
                }
            $nbkword = array_unique($nbkword,SORT_STRING);
            $temp_results[count($nbkword)][$id] = $movie;
            }

    krsort($temp_results);

    $results = array();
    $results['key_words'] = $key_words;
    $results['results'] = $temp_results;
    return $results;
}

/*--------------------------- CONVERT TO URL -----------------------*/
    function ValideUrl($string, $charset='utf-8') {
        $string = htmlentities($string, ENT_NOQUOTES, $charset);
        $string = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $string);
        $string = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $string); // pour les ligatures e.g. '&oelig;'
        $string = preg_replace('#&[^;]+;#', '', $string); // supprime les autres caractères
        // Mettez ici les caractères spéciaux qui seraient susceptibles d'apparaître dans les titres. La liste ci-dessous est indicative.
        $speciaux = array("?","!","@","#","%","&amp;","*","(",")","[","]","=","+"," ",";",":","'",".","_");
        $string = str_replace($speciaux, "-", $string); // Les caractères spéciaux dont les espaces, sont remplacés par un tiret.
        $string = strtolower(strip_tags($string));
        return $string;
    }

/*--------------------------- MESSAGE FLASH -----------------------*/

function setFlash($message, $type='success'){
    //creation de message flash
    $FLASH['F_message'] = $message;
    $FLASH['F_type'] = $type;    
    $_SESSION['Flash'][] = $FLASH;
    unset($FLASH);
}

function Flash(){
    if(isset($_SESSION['Flash'])){
        //si un message existe
        $ALERTE = '<div class="frame_alert">';
        foreach ($_SESSION['Flash'] as $kf => $FLASH) {
            //pour chaque message
            extract($FLASH); // extraire les variables
            $ALERTE .= "<div class='alert alert-$F_type'><span>$F_message</span></div>";//creation du div d'alerte
        
        }
    $ALERTE .= '</div>';
    unset($_SESSION['Flash']); // destruction des messages flash
    return $ALERTE; // renvoie de la partie html des messages flash
    }
}