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

$output['action'] = $_GET['action'] ?? '';
$output['text'] = stripslashes($_GET['text']) ?? '';

// text + edit -> edit
// only text -> learn
// else: mainpage

if ($output['text']) {	
	$output['urlEdit'] = "?action=edit&text=" . urlencode($output['text']);
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
<?PHP

include 'style.css'

?>
</style>

<script>
<?PHP

include 'main.js'

?>
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
  <!-- Commented out, because set to "jorges"
	<br>
<a href="javascript:location.href='http://del.icio.us/jorges?tags=sharedcards&notes=sharedCards+flashcards&url=' + encodeURIComponent(location.href);">To save click here</a>. -->

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
