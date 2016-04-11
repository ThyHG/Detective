console.log('hello');
var fake_questionnairre = {
	'q1': 'Are you healthy?',
	'q2': 'What is your favourite colour?',
	'q3': 'How do you know?',
	'q4': 'Do you like pointless questions?',
	'q5': 'What\'s the purpose of life?'
}

var fake_cards = [
	{
		'id': '1',
		'answers': {
			'a1': 'yes',
			'a2': 'blue',
			'a3': 'it\'s pretty',
			'a4': 'of course',
			'a5': 'I feel like it\'s more of a thing rather than a thing thing but more over less than equal'
		}
	},
	{
		'id': '2',
		'answers': {
			'a1': 'yes',
			'a2': 'red',
			'a3': 'it\'s pretty',
			'a4': 'of course',
			'a5': 'I feel like it\'s more of a thing rather than a thing thing but more over less than equal'
		}
	 },
	{
		'id': '3',
		'answers': {
			'a1': 'yes',
			'a2': 'yellow',
			'a3': 'it\'s pretty',
			'a4': 'of course',
			'a5': 'I feel like it\'s more of a thing rather than a thing thing but more over less than equal'
		}
	}
];

$(document).ready(function() {
	$('#enter-id').on('click', function(event){
		var id = $('input[name=code-insert]').val();
		getQuestions(id);
	})
	
});

getQuestions = function(id) {
	$('#input-code').hide();
	$('#start-questionnairre').show();

	// GET questions from server.
	$.ajax({
		type: 'GET',
		url: '../backend/getQuestions.php?id=' + id,
		dataType: 'json',
		success: function (data) {
			console.log(data)
			//Override test data with actual data on success
			fake_questionnairre = data;
		}
	}).fail( function (error) {
		console.log('error', error.statusText, this.url)
	})
	// Create input DOM elements for each question.
	for (var key in fake_questionnairre) {
	    if (Object.prototype.hasOwnProperty.call(fake_questionnairre, key)) {
	        var val = fake_questionnairre[key];
	        $('#start-questionnairre').append('<label>'+ val +'<input type="text" name="'+key+'"></label>')
	    }
	}
	// TODO change this into code thinger.
	$('#start-questionnairre').append('<button id="submit-questionnairre" type="button">Submit</button>');

	// Actions
	// On submission of questionnairre
	// TODO check field validation (everything filled in)
	$('#submit-questionnairre').on('click', function(event) {
		event.preventDefault();
		// TODO Do ajax request
		// SEND ID of person + answers
		$('#loading').show();
		$('#start-questionnairre').hide();
		window.setTimeout(showCards, 1000)
	})
}

showCards = function() {
	//Hide waiting screen
	$('#loading').hide();

	//New card instance (also used as 'subcards')
	//Data is directly from the cards object from server
	var Card = function(data) {
    	this.data = data;
	};

	//Add the render function to the prototype of card instance
	//Container: The parent element
	//Headrender: Is the to be rendered element a card or an answer?
	Card.prototype.render = function(container, headrender) {
		if(headrender){
			//If new card
	    	var card = $('<div data-id=' + this.data.id + ' class="card"></div>');
	    	card.appendTo(container);
	    	//Recursive call, the card needs to be filled with answers
	    	Card.renderCards(this.data.answers, card, false);
		} else {
			//If answers
		    var p = $("<p></p>", {
		    	text: this.data
		    });
	      	p.appendTo(container);
		}
	};
	//renderCards - Render cards or answers on them for each element in the object passed
	//cards: the javascript object that needs to be displayed
	//container: the parent object
	//headrender: Is it a card or answers to be passed along to render function.
	Card.renderCards = function(cards, container, headrender) {
	    $.each(cards, function(key, val) {
	        var c = new Card(val);
	        c.render(container, headrender);
	    });
	}
	//Create the cards.
	Card.renderCards(fake_cards, $("#cards"), true);
}

/* 
// 1. Get questionnairre from server
	// a. fake question set and put into form
// 2. Register request send questionnairre
	// a. Submit -> send question answers + ID to server
// 3. Waiting screen
// 4. Start game, receive cards and display them
//
*/ 