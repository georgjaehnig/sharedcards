<?PHP
/*
* sharedcards Version 0.1
*
******************
* New BSD License
******************
* Copyright (c) 2009, Georg Jaehnig
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*     * Redistributions of source code must retain the above copyright
*       notice, this list of conditions and the following disclaimer.
*     * Redistributions in binary form must reproduce the above copyright
*       notice, this list of conditions and the following disclaimer in the
*       documentation and/or other materials provided with the distribution.
*     * Neither the name of the <organization> nor the
*       names of its contributors may be used to endorse or promote products
*       derived from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY Georg Jaehnig ''AS IS'' AND ANY
* EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
* DISCLAIMED. IN NO EVENT SHALL Georg Jaehnig BE LIABLE FOR ANY
* DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
* (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
* ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
* (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
* SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

// ############################################################# PHP

function parseText($text)
{
	$javascriptMainCards = "new Array( ";

	// escape '
	//$text= str_replace("'", "\'", $text);
	$text = addslashes($text);

	$text = trim($text);
	$lines = explode("\n", $text);

	foreach ($lines as $line) {
		list($question, $answer) = explode("=", $line);
		$question = trim($question);
		$answer = trim($answer);

		if ($question && $answer) {
			$javascriptMainCards .= "new Array('$question','$answer',false),";
		}
	}

	// cut off last comma
	$javascriptMainCards = substr($javascriptMainCards, 0, strlen($javascriptMainCards)-1);
	$javascriptMainCards .= ")";
	return $javascriptMainCards;
}

$output['action'] = $_GET['action'];
$output['text'] = stripslashes($_GET['text']);

// text + edit -> edit
// only text -> learn
// else: mainpage

if ($output['text']) {	
	$output['urlEdit'] = "http://" . $_SERVER['HTTP_HOST'] . "/?action=edit&text=" . urlencode($output['text']);
	$output['javascriptMainCards'] = parseText($output['text']);	

	if ($output['action'] != 'edit') {	
		$output['action'] = 'learn';
	}
} else {
	$output['text'] = "love = amour\nquestion = answer\nWhat is the highest mountain of the world? = Mount Everest";
	$output['urlEdit'] = "javascript:setWindow('Edit');";
	$output['javascriptMainCards'] = "new Array()";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>sharedCards - a flashcard trainer in javascript</title>
<META http-equiv=Content-Type content="text/html; charset=UTF-8">
<meta name="description" content="Create and learn flashcards in your browser - even offline. No login, only javascript.">
<meta name="keywords" content="flashcards flashcard trainer learn vocabulary vokabeltrainer vokablen">
<style type="text/css">

/* ############################################################# CSS */

body {
	font-family:sans-serif;
	margin: 1em 2em;
}

dt {
	font-weight: bold;
}

#main {
	margin:auto;
	margin-top:1em;
	width: 40em;
}

#divMenu {
	margin-bottom: 0.3em;
}
div.menu {
	border: 1px solid #000080;
	display:inline;
	padding: 0.3em 0.6em;
	text-align: center;
	margin: 0.2em;
}
div.menuSelected {
	border: 1px solid #000080;
	border-bottom: 1px solid #ffffff;
	display:inline;
	padding: 0.3em 0.6em;
	text-align: center;
	margin: 0.2em;
}
div.menuInvisible {
	display:none;
}
a.menu:link, a.menu:visited {
	text-decoration:none;
}
div.window {
	display:none;
}
div.windowSelected {
	border: 1px solid #000080;
	border-bottom: none;
	padding: 1em;
	width: 40em;
	height: 20em;
	display:block;
}

/* ================================================ Edit Cards */

#textareaEdit {
	display:block;
	width:40em;
	height:20em;
	margin:1em 0em;
}
 
/* ================================================ Learn Cards */


#tableQuestioning {
	width:40em;
	table-layout: fixed;
	border-spacing: 0em;
	empty-cells: show;
}

#tdQuestion {
	height: 4em;
	text-align: center;
}
#tdInput {
	height: 7em;
	text-align: center;
}
#tdAnswer {
	height: 3em;
	text-align: center;
}
#tdValidation {
	height: 4em;
	text-align: center;
}
#tdStatistics {
	height: 1em;
	text-align: center;
}

div.learn, div.test, div.both {
}

#divQuestion {
	font-size: 1.5em;
}
#formCheck {
	margin:0px;
}
#inputAnswer {
	width:15em;
	font-size: 1.5em;
}
input.answers {
	visibility: hidden;
}
#inputCheck {
	display:inline;
	width: 7em;
}
#inputNext {
	width: 7em;
}
#inputRepeatFalse {
	display:none;
	width: 7em;
}
#inputRestart {
	display:inline;
}

#divCorrectAnswer {
	font-size: 1.5em;
}
#divValidation {
	display:inline;
	font-weight: bold;
	font-size: 1.5em;
}
div.correct {
	color: green;
}
div.false {
	color: red;
}
#divStatistics {
}
div.statisticsLabel {
	display:inline;
}

div.statisticsValue {
	display:inline;
	text-align:right;
	margin:0em 0.8em 0em 0.1em;
	background-color:#a0a0a0;
	color:#ffffff;
	padding:0.2em;
	
}

/* ================================================ Footer */

#footer {
	padding: 0.3em 1em;
	color: #ffffff;
	font-weight: bold;
	width: 40em;
	background-color: #c0c0c0;
	border: 1px solid #000080;
	border-top: none;
}

#footer a:link, #footer a:visited {
	color: #ffffff;
}

</style>

<script>

// ############################################################# Javascript

Array.prototype.hasElement= function(element)
// checks if element is element of array
// if yes: returns element postition
// if no: returns -1
{
	var array = this;
	for (var i=0; i<array.length; i++) {
		if (array[i] == element) {
			return i;
		}
	}
	return -1;
}

Array.prototype.mix= function()
// mixes the elements of an array
{
	var array = this;
	var itemTemp;
	for (var i=0; i < array.length; i++) {
		array[i][2] = false;
		itemTemp = array[i]; 
		randomNumber = Math.floor(Math.random() * array.length); 
		array[i] = array[randomNumber]; 
		array[randomNumber] = itemTemp; 
		// set false
	}
	return array;
}

String.prototype.trim= new Function("return this.replace(/^\\s+|\\s+$/g,'')");

function setWindow(name)
{
	for(
			var i=0; 
			current = document.getElementsByTagName("div")[i]; 
			i++
		) {
		if (current.className == "menuSelected") {
			current.className = "menu";
		}
		if (current.className == "windowSelected") {
			current.className = "window";
		}
	}
	if ((name == 'Learn') || (name == 'Test')) {
		document.getElementById('windowQuestioning').className = "windowSelected";
		for(
				var i=0; 
				current = document.getElementsByTagName("div")[i]; 
				i++
			) {
			if ((current.className == 'learn') || (current.className == 'test')) {
				if (current.className == name.toLowerCase()) {
					current.style.display = 'block';
				} else {
					current.style.display = 'none';
				}
			}
		}
	} else {
		document.getElementById('window' + name).className = "windowSelected";
	}
	document.getElementById('menu' + name).className = "menuSelected";
}

function arraySetFalse(arrayCards)
{
	for (var i=0; i < arrayCards.length; i++) {
		arrayCards[i][2] = false;
	}
	return arrayCards;
}

onload = function init()
{
	// document.getElementById('inputSaveUsername').value = 'jorges';
	// todo: get this from cookie
	
	setQuestioning();
	if (main['action'] == 'learn') {
		setWindow('Learn');
	} else {
		if (main['action'] == 'edit') {
			setWindow('Edit');
		} else if (main['action'] == '') {
			setWindow('Home');
		}
		document.getElementById('menuLearn').className = 'menuInvisible';
		document.getElementById('menuTest').className = 'menuInvisible';
		document.getElementById('menuSave').className = 'menuInvisible';
	}
}

function setQuestioning()
{
	if (main['cards'].length == 0) {
		return 0;
	}
	main['cards'] = main['cards'].mix();
	main['cards'] = arraySetFalse(main['cards']);
	main['currentCard'] = 0;
	main['correctAnswers'] = 0;
	main['askedCards'] = 0;

	document.getElementById('inputAnswer').style.visibility = 'visible';
	updateStatistics();
	setCurrentItem();
}

function setCurrentItem()
{
	if (main['currentCard'] >= main['cards'].length) {
		return;
	}
	// go to first false card
	while(main['cards'][main['currentCard']][2]) {
		main['currentCard']++;
	}

	document.getElementById('divQuestion').firstChild.data = main['cards'][main['currentCard']][0];
	document.getElementById('divCorrectAnswer').style.visibility = 'hidden';
	document.getElementById('divCorrectAnswer').firstChild.data = '.';
	document.getElementById('divValidation').style.visibility = 'hidden';
	document.getElementById('divValidation').firstChild.data = '.';
	document.getElementById('inputAnswer').value = '';
	document.getElementById('inputAnswer').readOnly = false;
	document.getElementById('inputNext').style.display = 'none';
	document.getElementById('inputCheck').style.display = 'inline';
	document.getElementById('formCheck').action = 'javascript:checkAnswer(document.getElementById("inputAnswer").value);';
	//document.getElementById('inputAnswer').focus();

	// set multiple choice answers
	main['answers'] = new Array(main['cards'][main['currentCard']][1]);

	var iRandom;
	for(var i=1; (i<=2) && (i<=main['cards'].length-1); i++) {
		do {
			iRandom = Math.floor(Math.random() * main['cards'].length);
		} while(main['answers'].hasElement(main['cards'][iRandom][1]) > -1 || (main['currentCard'] == iRandom));
		main['answers'][i] = main['cards'][iRandom][1];
	}
	main['answers'] = main['answers'].mix();
	for(var i=1; (i<=3) && (i<=main['cards'].length); i++) {
		document.getElementById('inputAnswer'+i).value = main['answers'][i-1];
		document.getElementById('inputAnswer'+i).onclick = new Function('evt', 'checkAnswer(document.getElementById("inputAnswer' + i + '").value);');
		document.getElementById('inputAnswer'+i).style.visibility = 'visible';
	}
}	

function updateStatistics()
{
	document.getElementById('divCorrectAnswers').firstChild.data = main['correctAnswers'];
	document.getElementById('divAskedCards').firstChild.data = main['askedCards'];
	document.getElementById('divAllCards').firstChild.data = main['cards'].length;
	main['correctAnswersPercent'] = 
		main['correctAnswers'] * 100
		/
		(main['askedCards'] ? main['askedCards'] : 1);
	document.getElementById('divCorrectAnswersPercent').firstChild.data = Math.round(main['correctAnswersPercent']);
}

function repeatFalse()
{
	main['currentCard'] = 0;
	main['askedCards'] = main['correctAnswers'];
	main['cards'] = main['cards'].mix();
	document.getElementById('inputRepeatFalse').style.display = 'none';
	updateStatistics();
	setCurrentItem();
}

function checkAnswer(answer)
{
	main['askedCards']++;
		
	document.getElementById('divCorrectAnswer').style.visibility = 'visible';
	document.getElementById('divCorrectAnswer').firstChild.data = main['cards'][main['currentCard']][1];
	document.getElementById('inputAnswer').readOnly = true;
	
	if (
			// inputAnswer == answer
			(answer == main['cards'][main['currentCard']][1])
			||
			//answer = 'one, two, three' -> inputAnswer == 'one' or 'two' or 'three'
			(main['cards'][main['currentCard']][1].split(/\s*(,|;)\s*/).hasElement(answer) > -1)
		)
		{
		document.getElementById('divValidation').style.visibility = 'visible';
		document.getElementById('divValidation').firstChild.data = 'correct';
		document.getElementById('divValidation').className= 'correct';
		main['correctAnswers']++;
		main['cards'][main['currentCard']][2] = true;
	} else {
		document.getElementById('divValidation').style.visibility = 'visible';
		document.getElementById('divValidation').firstChild.data = 'false';
		document.getElementById('divValidation').className= 'false';
	}

	updateStatistics();

	var addToInputAnswers = '';

	document.getElementById('inputCheck').style.display = 'none';
	if (main['askedCards'] < main['cards'].length) {
		document.getElementById('inputNext').style.display = 'inline';
		document.getElementById('formCheck').action = 'javascript:setCurrentItem();';
		addToInputAnswers = document.getElementById('inputNext').value;
	} else if (main['correctAnswers'] < main['cards'].length) {
		document.getElementById('inputRepeatFalse').style.display = 'inline';
		document.getElementById('formCheck').action = 'javascript:repeatFalse();';
		addToInputAnswers = document.getElementById('inputRepeatFalse').value;
	} else {
		document.getElementById('formCheck').action = 'javascript:{};';
	}
	//document.getElementById('inputCheck').focus();

	for(var i=1; i<=3; i++) {
		if (main['cards'][main['currentCard']][1] == document.getElementById('inputAnswer'+i).value) {
			document.getElementById('inputAnswer'+i).onclick = new Function('evt', document.getElementById('formCheck').action);
			document.getElementById('inputAnswer'+i).value += ' ... ' + addToInputAnswers;
		} else {
			document.getElementById('inputAnswer'+i).style.visibility = 'hidden';
			document.getElementById('inputAnswer'+i).onclick = '';
		}
	}

	main['currentCard']++;
}	


function swapQuestionsAnswers()
{
	// array of lines
	lines = document.getElementById('textareaEdit').value.split(/\s*\n\s*/);
	
	// delete old text
	document.getElementById('textareaEdit').value = '';
	
	var card;
	
	// for every line
	for (var i=0; i<lines.length; i++) {
		if (lines[i].search(/\=/) != -1) {
			// split at =
			card = lines[i].split(/\s*=\s*/);
	
			// swap and add line to new text;
			document.getElementById('textareaEdit').value += card[1] + ' = ' + card[0] + "\n";
		} else {
			document.getElementById('textareaEdit').value += lines[i] + "\n";
		}
	}
	for (var i=0; i<main['cards'].length; i++) {
		card = main['cards'][i][0];
		main['cards'][i][0] = main['cards'][i][1];
		main['cards'][i][1] = card;
	}
	setCurrentItem();
}

function findCards()
{
	// array of lines
	var lines = document.getElementById('textareaEdit').value.split(/\s*\n\s*/);
	
	// delete old text
	document.getElementById('textareaEdit').value = '';
	
	var card;
	
	// for every line
	for (var i=0; i<lines.length; i++) {
		lines[i] = lines[i].trim();
		lines[i] = lines[i].replace(/(\s{2,}|\t+)/, '=');
		document.getElementById('textareaEdit').value += lines[i] + "\n";
	}
}


function checkKeypress(key)
{
/*	if ((key>=49) && (key<=51) && (node = document.getElementById('inputAnswer' + (key-48)))) {
		alert(1);
		//node.onclick();
	}
*/}

var main = new Object();

main['cards'] = <?PHP echo $output['javascriptMainCards'] ?>;
main['action'] = '<?PHP echo $output['action'] ?>';

</script>

</head>
<body onKeyDown="checkKeypress(event.keyCode);">

<!-- ############################################################# HTML -->

<div id="main">

<div id="divMenu">
	<div class="menu" id="menuHome">
	<a class="menu" href="javascript:setWindow('Home');">Home</a>
	</div>
	<div class="menu">
	<a class="menu" href="http://del.icio.us/jorges/sharedcards">Cards &gt;</a>
	</div>
	<div class="menu" id="menuEdit">
	<a class="menu" href="<?PHP echo $output['urlEdit'] ?>">Edit</a>
	</div>
	<div class="menu" id="menuLearn">
	<a class="menu" href="javascript:setWindow('Learn');">Learn</a>
	</div>
	<div class="menu" id="menuTest">
	<a class="menu" href="javascript:setWindow('Test');">Test</a>
	</div>
	<div class="menu" id="menuSave">
	<a class="menu" href="javascript:setWindow('Save');">Save</a>
	</div>
</div>
<div class="window" id="windowHome">

<p><em>sharedcards</em> is a <strong>online flashcard trainer</strong> in Javascript. Use it for learning vocabulary of a foreign language or any other pair items. Save your flashcards on+offline or <strong>share them with the world</strong>.

<ul>
<li><big><a href="<?PHP echo $output['urlEdit'] ?>">Create new flashcards</a></big> or
<li><big>learn <a href="http://del.icio.us/tag/sharedcards">flashcards of others</a></big>
</ul>

</div>
<div class="window" id="windowEdit">
	
	<form id="formEdit" action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="get">
		<input type="hidden" name="action" value="learn">
		<textarea id="textareaEdit" name="text" wrap="off"><?PHP echo $output['text'] ?></textarea>

		<input id="inputLearn" type="submit" value="Compile + Learn">
		<a href="javascript:swapQuestionsAnswers();">Swap questions + answers</a>
		<a href="javascript:findCards();">Find cards</a>
	</form>

</div>
<div class="window" id="windowQuestioning">

	<form id="formCheck" action="" >

		<table id="tableQuestioning">
			<tr>
				<td id="tdQuestion">
					<div id="divQuestion" class="both">.</div>
				</td>
			</tr>
			<tr>
				<td id="tdInput">
					<div class="test">
						<input id="inputAnswer" class="test" type="text">
						<input id="inputCheck" class="test" type="submit" value="Check">
						<input id="inputNext" type="submit" value="Next">
						<input id="inputRepeatFalse" type="submit" value="Repeat false">
					</div>
					<div class="learn">
						<ol id="olAnswers">
							<li><input id="inputAnswer1" class="answers" type="button" onclick="alert(0);"></li>
							<li><input id="inputAnswer2" class="answers" type="button" onclick="alert(0);"></li>
							<li><input id="inputAnswer3" class="answers" type="button" onclick="alert(0);"></li>
						</ol>
					</div>
				</td>
			</tr>
			<tr>
				<td id="tdAnswer"><div id="divCorrectAnswer" class="both">.</div></td>
			</tr>
			<tr>
				<td id="tdValidation">
					<div id="divValidation" class="both">.</div>		
				</td>
			</tr>
			<tr>
				<td id="tdStatistics">
					<div id="divStatistics">
						<div class="statisticsLabel" id="divCorrectAnswersLabel">Correct:</div>
						<div class="statisticsValue" id="divCorrectAnswers">0</div>
						<div class="statisticsLabel" id="divAskedCardsLabel">Asked:</div>
						<div class="statisticsValue" id="divAskedCards">0</div>
						<div class="statisticsLabel" id="divAllCardsLabel">All:</div>
						<div class="statisticsValue" id="divAllCards">0</div>
						<div class="statisticsLabel" id="divCorrectAnswersPercentLabel">%:</div>
						<div class="statisticsValue" id="divCorrectAnswersPercent">0</div>
						<input id="inputRestart" type="button" value="Restart" onClick="setQuestioning();"> 
						<input id="inputRestart" type="button" value="Swap questions/answers"onclick="swapQuestionsAnswers();">
					</div>
				</td>
			</tr>
		</table>
	</form>

</div>

<div class="window" id="windowSave">
There are 3 ways to save your flashcards:
<dl>
	<dt>on your harddisk</dt>
	<dd>Save this file now in your browser (File, Save as). Open it and learn while you are on- or offline.</dd>

	<dt>in your bookmarks</dt>
	<dd>bookmark this page now. To learn click on the bookmark, you need to be online.</dd>

	<dt>at <em>del.icio.us</em></dt>
	<dd>Your flashcards will be shared with others and available at the <strong>Cards</strong> button. You need an <a href="http://del.icio.us/register">account</a> at <a href="http://del.icio.us/">del.icio.us</a>. Use tags to describe your cards like <em>sharedcards german english fruits vegetables</em>.
	<br>
	<a href="javascript:location.href='http://del.icio.us/jorges?tags=sharedcards&notes=sharedCards+flashcards&url=' + encodeURIComponent(location.href);">To save click here</a>. 

	</dd>
</dl>
</div>
<div id="footer">
sharedcards
|
<a href="http://jaehnig.org/wiki/Talk:SharedCards">Your comment here!</a>
|
<a href="http://jaehnig.org/wiki/Imprint">Imprint</a>
|
<a href="https://github.com/georgjaehnig/sharedcards">Source code</a>
</div>

</div>

</body>
</html>
