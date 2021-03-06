 <?php

//--------------------------------CONNEXION A LA BASE DE DONNEES-------------------------------->
try {
    $db = new PDO ('mysql:host=localhost:8889;dbname=events;charset=utf8',
                    'root', 'root', array (PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)); //root: identifiant et psw de la bdd
} catch (PDOException $e) {
    print "Erreur! :" . $e->getMessage () . "<br/>"; //parametres supplementaires pour voir toutes les erreurs
    die();
}

//--------------------------------LECTURE DE LA BASE DE DONNEES---------------------------------->
$reponse = $db->query('SELECT * FROM events');
while ($infos = $reponse->fetch (PDO::FETCH_ASSOC)) {  //FETCH dans un while pour passer le tableau en revue ligne a ligne
}                                                 
?>

<?php
//-------------------------------SESSIONS + CONTROLE -> changement de mois---------------------->	
session_start();

// L'utilisateur est-il identifié?
if ( ! isset( $_COOKIE['user'] ) )
{
	// Si non, attribution d'une chaine aléatoire
	setcookie( 'user', rand(), time()+60*60*24*30 ); // 30j en secondes -----> sera appelé apres pour remplir 'creator'
}
else
{
	// Si oui, on prolonge de 30j le cookie
	setcookie( 'user', $_COOKIE['user'], time()+60*60*24*30 ); // 30j en secondes	
}

// Mois courant passé par paramètre
if ( isset( $_REQUEST['month'] ) )
{
	$current_month = (int)$_REQUEST['month'];
}
// Mois enregistré en cookie 
elseif ( isset( $_COOKIE['current_month'] ) )
{
	$current_month = (int)$_COOKIE['current_month'];
}
else
{
	$current_month = date( 'n' );
}

// Année courante passé par paramètre
if ( isset( $_REQUEST['year'] ) )
{
	$current_year = (int)$_REQUEST['year'];
} 
// Annnée enregistrée en cookie 
elseif ( isset( $_COOKIE['current_year'] ) )
{
	$current_year = (int)$_COOKIE['current_year'];
}
else
{
	$current_year = date( 'Y' );
}

// Enregistrement en cookies
setcookie( 'current_month', $current_month, time()+60*60*24*30 ); // 30j en secondes 
setcookie( 'current_year', $current_year, time()+60*60*24*30 ); // 30j en secondes 

//--------------------------------------ENREGISTRER UN EVENT (en session -> BdD: TP)------------------------------->	

// Enregistrement d'un événement
if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'save' && isset( $_REQUEST['date'] ) )
{
	$title = isset( $_REQUEST['title'] ) ? $_REQUEST['title'] : '';

	// Récupération des précédents événements
	$events = $_SESSION['events'];

	$new_event = array( 'title' => $title );
	
	// Bonus: ajout d'une image
	if ( isset( $_FILES['image'] ) && $_FILES['image']['size'] )
	{
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
			
		if ( finfo_file($finfo, $_FILES['image']['tmp_name']) == 'image/jpeg' )
		{
			move_uploaded_file( $_FILES['image']['tmp_name'], '/' . $_FILES['image']['name'] );
			
			$new_event['image'] = $_FILES['image']['name'];
		}
	}

	// 1.Ajout de l'événement
	//$events[$_REQUEST['date']][] = $new_event;

	//enregistrer entant que variable
	$title = $_REQUEST['title'];
	$date = $_REQUEST['date'];
	$image_name = $_REQUEST['image_name'];

	
	// 2.Enregistrement en session
	//$_SESSION['events'] = $events; 

	//enregistrement dans la base de données
	$db->exec('INSERT INTO `events` SET `date` = "'.$date.'", `title` = "'.$title.'", `image_name` = "'.$image_name.'", `creator` = "'.$_COOKIE['user'].'"');

}
?>

<!-----------------------------------------------------CODE HTML + CSS--------------------------------------------->

<html lang="en" class="">
<head>
	<meta charset="UTF-8">
	<meta name="robots" content="noindex">
	<style class="cp-pen-styles" type="text/css">
	* {
		-webkit-font-smoothing: antialiased;
	}

	body {
		font-family: 'helvetica neue';
		background-color: #A25200;
		margin: 0;
	}

	.wrapp {
		width: 450px;
		margin: 30px auto;
		flex-direction: row;
		flex-wrap: wrap;
		justify-content: center;
		align-content: center;
		align-items: center;
		box-shadow: 0 0 10px rgba(54, 27, 0, 0.5);
	}

	.flex-calendar .days,.flex-calendar .days .day.selected,.flex-calendar .month,.flex-calendar .week{
		display:-webkit-box;
		display:-webkit-flex;
		display:-ms-flexbox;
	}
	.flex-calendar{
		width:100%;
		min-height:50px;
		color:#FFF;
		font-weight:200
	}
	.flex-calendar .month {
		position:relative;
		display:flex;
		flex-direction:row;
		flex-wrap: nowrap;
		-webkit-justify-content:space-between;
				justify-content:space-between;
		align-content:flex-start;
		align-items:flex-start;
		background-color:#ffb835;
	}
	
	.flex-calendar .month .arrow,.flex-calendar .month .label {
		height:60px;
		order:0;
		flex:0 1 auto;
		align-self:auto;
		line-height:60px;
		font-size:20px;
	}
	
	.flex-calendar .month .arrow {
		width:50px;
		box-sizing:border-box;
		background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAABqUlEQVR4Xt3b0U3EMBCE4XEFUAolHB0clUFHUAJ0cldBkKUgnRDh7PWsd9Z5Tpz8nyxFspOCJMe2bU8AXgG8lFIurMcurIE8x9nj3wE8AvgE8MxCkAf4Ff/jTEOQBjiIpyLIAtyJpyFIAjTGUxDkADrjhxGkAIzxQwgyAIPxZgQJAFJ8RbgCOJVS6muy6QgHiIyvQqEA0fGhAArxYQAq8SEASvHTAdTipwIoxk8DUI2fAqAc7w6gHu8KkCHeDSBLvAtApng6QLZ4KkDGeBpA1ngKQOb4YYDs8UMAK8SbAVaJNwGsFN8NsFq8FeADwEPTmvPxSXV/v25xNy9fD97v8PLuVeF9FiyD0A1QKVdCMAGshGAGWAVhCGAFhGGA7AgUgMwINICsCFSAjAh0gGwILgCZENwAsiC4AmRAcAdQR5gCoIwwDUAVYSqAIsJ0ADWEEAAlhDAAFYRQAAWEcIBoBAkAIsLX/rV48291MgAEhO747o0Rr82J23GNS+6meEkAw0wwx8sCdCAMxUsDNCAMx8sD/INAiU8B8AcCLT4NwA3CG4Az68/xOu43keZ+UGLOkN4AAAAASUVORK5CYII=) no-repeat;
		background-size:contain;
		background-origin:content-box;
		padding:15px 5px;
		cursor:pointer;
	}
	
	.flex-calendar .month .arrow:last-child {
		-webkit-transform:rotate(180deg);
			-ms-transform:rotate(180deg);
				transform:rotate(180deg);
	}
	
	.flex-calendar .month .arrow.visible {
		opacity:1;
		visibility:visible;
		cursor:pointer;
	}
	
	.flex-calendar .month .arrow.hidden {
		opacity:0;
		visibility:hidden;
		cursor:default;
	}
	
	.flex-calendar .days,.flex-calendar .week {
		line-height:25px;
		font-size:16px;
		display:flex;
		-webkit-flex-wrap: wrap;
				flex-wrap: wrap;
	}
	
	.flex-calendar .days {
		background-color:#FFF;
	}
	
	.flex-calendar .week {
		background-color:#faac1c;
	}
	
	.flex-calendar .days .day,.flex-calendar .week .day {
		flex-grow:0;
		-webkit-flex-basis: calc( 100% / 7 );
		min-width: calc( 100% / 7 );
		text-align:center;
	}
	
	.flex-calendar .days .day {
		min-height:60px;
		box-sizing:border-box;
		position:relative;
		line-height:60px;
		border-top:1px solid #FCFCFC;
		background-color:#fff;
		color:#8B8B8B;
		-webkit-transition:all .3s ease;
				transition:all .3s ease;
	}
	
	.flex-calendar .days .day.out {
		background-color:#fCFCFC;
	}
	
	.flex-calendar .days .day.disabled.today,.flex-calendar .days .day.today {
		color:#FFB835;
		border:1px solid;
	}
	
	.flex-calendar .days .day.selected {
		display:flex;
		flex-direction:row;
		flex-wrap:nowrap;
		-webkit-justify-content:center;
				justify-content:center;
		align-content:center;
		-webkit-align-items:center;
				align-items:center;
	}
	
	.flex-calendar .days .day.selected .number {
		width:40px;
		height:40px;
		background-color:#FFB835;
		border-radius:100%;
		line-height:40px;
		color:#FFF;
	}
	
	.flex-calendar .days .day:not(.disabled):not(.out) {
		cursor:pointer;
	}
	
	.flex-calendar .days .day.disabled {
		border:none;
	}
	
	.flex-calendar .days .day.disabled .number {
		background-color:#EFEFEF;
		background-image:url(data:image/gif;base64,R0lGODlhBQAFAOMAAP/14////93uHt3uHt3uHt3uHv///////////wAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAAAALAAAAAAFAAUAAAQL0ACAzpG0YnonNxEAOw==);
	}
	
	.flex-calendar .days .day.event:before {
		content:"";
		width:6px;
		height:6px;
		border-radius:100%;
		background-color:#faac1c;
		position:absolute;
		bottom:10px;
		margin-left:-3px;
	}
	
	.flex-calendar .days .day .infos{
		padding: 5px 10px;
		position: absolute;
		left: 50%; top: 100%;
		-webkit-transform: translateX(-50%);
				transform: translateX(-50%);
		z-index: 1;
		background: #faac1c;
		color: white;
		font-size: 14px;
		font-weight: bold;
		line-height: normal;
		white-space: nowrap;
		opacity: 0;
		pointer-events: none;
		-webkit-transition:all .3s ease;
				transition:all .3s ease;
	}
	.flex-calendar .days .day:hover .infos{ opacity: 1 }
	
	form{
		padding: 20px;
		position: relative;
		background: white;
		box-sizing: border-box;
	}
	
	form p{ margin: 0 }
	form p + p{ margin-top: 20px }
	
	form label{ color: #8B8B8B }
	
	form input{
		height: 30px;
		font-size: 12px;
	}
	
	form button{
		padding: 10px 20px;
		position: absolute;
		right: 20px; bottom: 20px;
		background: #faac1c;
		border: none;
		color: white;
		font-size: 18px;
	}
	
	#events_list{
		padding: 20px;
		box-sizing: border-box;
		background: white;
		color: #8b8b8b
	}
	
	#events_list h2{ margin: 0; font-weight: normal }
	
	#events_list a{
		font-size: 12px;
		color: #faac1c;
		text-decoration: none;
	}
	#events_list a:hover{ text-decoration: underline }
	</style>

	<title>Calendrier</title>
</head>

<body>
	<div class="wrapp">
		<div class="flex-calendar">

<!-----------------------------------------------PHP CHANGEMENT MOIS DYNAMIQUE --------------------------------->		
			
			<?php
				// Mois/année en cours			
				$this_month = strtotime( $current_year . '-' . $current_month );
			
				// Mois précédent - méthode 1
				if ( $current_month == 1 )
				{
					$previous_month = 12;
					$previous_year = $current_year - 1;
				}
				else
				{
					$previous_month = $current_month - 1;
					$previous_year = $current_year;
				}
				
				// Mois suivant - méthode 1
				if ( $current_month == 12 )
				{
					$next_month = 1;
					$next_year = $current_year + 1;
				}
				else
				{
					$next_month = $current_month + 1;
					$next_year = $current_year;
				}
			?>
			<!--------------------------------------FLECHES ACTIVES------------------------------------------>
			<div class="month">
				<a href="calendrier_exo.php?year=<?php echo $previous_year ?>&month=<?php echo $previous_month ?>" class="arrow visible"></a>
				
				<div class="label">
					<?php echo date( 'F Y', $this_month );?>
				</div>

				<a href="calendrier_exo.php?year=<?php echo $next_year ?>&month=<?php echo $next_month ?>" class="arrow visible"></a>
			</div>

			<div class="week">
				<div class="day">M</div>
				<div class="day">T</div>
				<div class="day">W</div>
				<div class="day">T</div>
				<div class="day">F</div>
				<div class="day">S</div>
				<div class="day">S</div>
			</div>

			<div class="days">
				
			<?php
				
				// Bornes du mois courant
				$first_day_of_month = date( 'N', strtotime( 'first day of ' . $current_year . '-' . $current_month ) );
				$last_day_of_month = date( 'd', strtotime( 'last day of ' . $current_year . '-' . $current_month ) );
				
//-------------------------------------------AFFICHAGE JOURS / JOURS SPECIAUX ------------------------------------->					

				$today = new DateTime( 'today' );
				$disabled = array( new DateTime( '2018-05-21' ) );
				$events = array();
				
				// Récupération des événements en session
				#$events = $_SESSION['events']; 
								
				// Décalage premier jour du mois
				for ( $i = 1; $i < $first_day_of_month; $i++ )
				{
					echo '<div class="day out"><div class="number"></div></div>';
				}
				
				$reponse = $db->query('SELECT * FROM events');
				$eventsall = $reponse->fetchAll (PDO::FETCH_ASSOC);

				// Calendrier
				for( $i = 1; $i <= $last_day_of_month; $i++ )
				{
					$infos = '';
					$classes = 'day';
					
					// Convertion du jour en cours en objet DateTime
					$current_day = new DateTime( $current_year . '-' . $current_month . '-' . $i );

					// Aujourd'hui?
					if ( $current_day == $today ) $classes .= ' selected';
					
					// Jour désactivé
					if ( in_array( $current_day, $disabled ) ) $classes .= ' disabled';

					//Creation new tableau avec tous les events -> pastille aux jours à events
					foreach ($eventsall as $element) {
						if ($element['date'] == $current_day->format ('Y-m-d'))
							$classes .= ' event'; //.= pour coller ensemble sinon ca affecte et ecrase le reste
					}
					$events = '<div class="infos">' . $event_text . '</div>';

					
					echo '<div class="' . $classes . '"><div class="number">' . $i . '</div>' . $infos . '</div>'; //affichage general 
				}
			?>
			
			</div>
		</div>
	</div>
	
<!-------------------------------------------------HTML DU FORMULAIRE ------------------------------------->

	<form class="wrapp" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="save" />
		<p>
			<label for="date">Date</label>
			<input type="date" name="date" id="date" value="<?php echo date( 'Y-m-d' ) ?>" required />
		</p>
		<p>
			<label for="title">Titre</label>
			<input type="text" name="title" id="title" size="40" value="" />
		</p>
		<p>
			<label for="image">Image</label>
			<input type="file" name="image" id="image" />
		</p>
		<button type="submit">Valider</button>
	</form>

<!------------------------------------------LISTER LES EVENTS A VENIR ----------------------------------->

	<div class="wrapp" id="events_list">
		<h2>Evénements</h2>
		<ul>

<!------------------------------------LECTURE DE LA BASE DE DONNEES-------------------------------------->

			<?php
			$reponse = $db->query('SELECT * FROM events');
			while ($infos = $reponse->fetch (PDO::FETCH_ASSOC)) {  
				echo '<pre>';
				print_r ($infos); //print_r pour afficher le contenu d'une variable
				echo'</pre>'; 
}
			?>
		</ul>
	</div>
	
</body>
</html>
