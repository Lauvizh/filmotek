<?php

/*--------------------------- CONNECTION A LA BASE -----------------------*/
function dbConnection(){
    global $CONFIG;
    try{
        $db = new PDO($CONFIG['dsn'],$CONFIG['utilisateur'],$CONFIG['mot_passe']);
        $db->exec('SET CHARACTER SET UTF8');
    }catch(PDOException $e){
        die('<h1>Haaaaaaa !!!!!</h1>'.$e->getMessage());
    }
    return $db;
}

/*--------------------------- VERIF EMAIL -----------------------*/
function isEmail($adresse){
    if (filter_var($adresse, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
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
    $req_U = $db->prepare("SELECT id,email,pseudo,rang,passhash FROM ".$U." WHERE email = :email");
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

/*--------------------------- GET LAST ADDED -----------------------*/

function getLastAdd($category){
    global $db;
    global $CONFIG;
    //requette
    if (!is_string($category)) {
        throw new Exception('wrong type category.');
        }
    else{
        $r = $db->query('SELECT id,title,date_add FROM '.$CONFIG['PrefixDB'].'List WHERE category = "'.$category.'" ORDER BY date_add DESC, title DESC LIMIT '. $CONFIG['movies_lastadded'])->fetchall(PDO::FETCH_ASSOC);
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
            return $r;
        }        
    }
}

/*--------------------------- GET SEARCH RESULTS -----------------------*/
function getSearchResults($request){
    global $db;
    global $CONFIG;
    $infofields = array('title','title_orig','actors','directors','year');
    $results = array();
    $temp_results = array();
    //requette
    $key_words = explode(" ",$request);
    $nbkey_words = count($key_words);

    foreach ($key_words as $key_word) {
        if (strlen($key_word) >= 3) {
            foreach ($infofields as $field) {
                $result = $db->query('SELECT id,title,title_orig,actors,directors,year FROM '.$CONFIG['PrefixDB'].'List WHERE '.$field.' REGEXP "([[:<:]])'.$key_word.'([[:>:]])" ORDER BY year DESC, title ASC')->fetchall(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    foreach ($result as $movie) {
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


    for ($i=$nbkey_words; $i > 0 ; $i--) {
        foreach ($results as $id => $result) {
            foreach ($result['search'] as $n => $field) {
                if (count($field) == $i) {
                    $temp_results[$id] = $result;
                }
            }
        }

    }
    $results = $temp_results;
    return $results;
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