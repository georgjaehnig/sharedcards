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

