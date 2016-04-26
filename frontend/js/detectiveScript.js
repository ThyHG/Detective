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
		'nick': 'Bob',
		'answers': {
			'a1': 'yes',
			'a2': 'blue',
			'a3': 'it\'s pretty',
			'a4': 'of course',
			'a5': 'I feel like it\'s more of a thing rather than a thing thing but more over less than equal'
		},
		'status': 'unsolved'
	},
	{
		'id': '2',
		'nick': 'Aaron',
		'answers': {
			'a1': 'yes',
			'a2': 'red',
			'a3': 'it\'s pretty',
			'a4': 'of course',
			'a5': 'I feel like it\'s more of a thing rather than a thing thing but more over less than equal'
		},
		'status': 'solved'
	 },
	{
		'id': '3',
		'nick': 'Shaniqua',
		'answers': {
			'a1': 'yes',
			'a2': 'yellow',
			'a3': 'it\'s pretty',
			'a4': 'of course',
			'a5': 'I feel like it\'s more of a thing rather than a thing thing but more over less than equal'
		},
		'status': 'raar'
	}
];
//On ready, bind value field to generate ID.
$(document).ready(function() {
	$('#enter-id').on('click', function(event){
		var id = $('input[name=code-insert]').val();
		var nick = $('input[name=name-insert]').val();
		getQuestions(id, nick);
	})
	
});
//Request questions from server, dynamically create questions, buttons and clickhandlers
getQuestions = function(id, nick) {
	$('#input-code').hide();
	$('#questionnaire-container').show();

	// GET questions from server.
	$.ajax({
		type: 'GET',
		url: '../backend/getQuestions.php?id=' + id,
		dataType: 'json',
		success: function (data) {
			console.log(data)
			if(typeof data === 'string') {
				console.log('User has filled in questions', id);
				gameStartCheck(id);
				$('#loading').show();
				$('#questionnaire-container').hide();
			} else if (typeof data === 'object'){
				console.log('User hasn\'t filled in questions')
				//Override test data with actual data on success
				fake_questionnairre = data;
			}
		}
	}).fail(function (error) {
		console.log('error', error.statusText, this.url)
	}).always(function () {
		//TODO: ONLY RENDER ON SUCCESS.
		// Create input DOM elements for each question.
		for (var key in fake_questionnairre) {
		    if (Object.prototype.hasOwnProperty.call(fake_questionnairre, key)) {
		        var val = fake_questionnairre[key];
		        $('#start-questionnairre').append('<label class="six offset-by-three columns">'+ val +'<input type="text" name="'+key+'" class="twelve columns"></label>')
		    }
		}
		// submit button.
		$('#start-questionnairre').append('<button id="submit-questionnairre" class="six offset-by-three columns button-primary" type="button">Submit</button>');

		// Action
		// On submission of questionnairre
		// TODO check field validation (everything filled in)
		$('#submit-questionnairre').on('click', function(event) {
			event.preventDefault();
			sendAnswers(id, nick);
		})
	})
}
//sendAnswers: sends the answers given in the questionnaire back to the server.
sendAnswers = function(id, nick) {

	//placeholder object to send
	var answersObject = {
		id: id,
		nick: nick,
		answers: {}
	}
	//gather all the datafields.
	var answers = $('#start-questionnairre').serializeArray();
	for(i=0; i<answers.length; i++) {
		//transform serialized data into a readable object
		answersObject.answers[i] = answers[i].value;
	}

	// SEND ID of person + answers
	$.ajax({
		type: 'POST',
		url: '../backend/submitQ.php',
		data: answersObject,
		success: function(msg) {
			console.log(msg)
		}
	}).fail( function (error) {
		console.log('error', error)
		//REMOVEME - fills in an extra card when no server connection.
		fake_cards[fake_cards.length] = answersObject;
		console.log(fake_cards)
	});
	//Loading screen!
	$('#loading').show();
	$('#questionnaire-container').hide();
	//Check if game has started
	gameStartCheck(id);
};

//periodically check if the game has launched,
// if launched, get cards from server.
gameStartCheck = function (id) {
	console.log('game start check', id)
	//I suck at planning.
	$('#cards').data('id', id);
	console.log($('#cards'));
	$.ajax({
	    type:'GET',
	    url:'../backend/status.php',
	    error: function(error) {
	        //file not exists
	        console.log('error', error);
	        // TODO this line is for testing without server only.
	        // window.setTimeout(showCards, 1000)
	    },
	    success: function(string) {
	        if(string === 'online') {
	        	console.log('Game started', id);
	        	getCards(id);
	    	} else {
	    		//try again latehurr.
	    		// window.setTimeout(function() { gameStartCheck(id) }, 3000);
	    		window.setTimeout(gameStartCheck.bind(null, id), 4000);
	    	}
	    }
	});
};
//Get cards from server
getCards = function(id) {
	//if we get here from the moarbutton, hide it.
	$('#moar-button').hide();
	console.log('getCards', id);
	$.ajax({
		type: 'GET',
		url: '../backend/getCards.php?id=' + id,
		dataType: 'json',
		success: function(cards) {
			console.log('cards: ' + cards);
			showCards(cards);
		},
		error: function(error) {
			console.log('error: ' + error);

		}
	})
}

showCards = function(cards) {
	//Hide waiting screen
	$('#loading').hide();
	$('#cards').show();
	var amtcards = 1;
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
	    	var card = $('<div data-id=' + this.data.id + ' data-solved="'+ this.data.status+'" data-nick="'+this.data.nick+'" class="twelve columns card"><h4 class="card-title">Person #'+ amtcards +'</h4></div>');
	    	amtcards++;
	    	card.appendTo(container);
			//If solved card
			if(this.data.status === 'solved') {
				//append success message

	    	} else {
	    		//If unsolved card
		    	//Recursive call, the card needs to be filled with answers
		    	Card.renderCards(this.data.answers, card, false);
	    	}
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
	//Create the cards. -- REMOVEME TODO -- currently falls back to fake cards when no server hooked up
	var cardsToRender = cards ? cards : fake_cards;
	console.log(cards, cardsToRender)
	Card.renderCards(cardsToRender, $("#cards"), true);
	bindCards();
}
//Bind the click function on the button, checks value of input field with the ID of card.
// Jezus wtf is dit eigenlijk. Lrn 2 code pls.
bindCards = function() {
	var cards = $('.card');
	for(i=0; i<cards.length; i++) {
		//If the card is solved, don't render the answers
		if(cards[i].dataset.solved !== 'solved'){
			var input = $('<input type="text" placeholder="This persons\'s ID" class="card-input">');
	        var button = $('<button class="card-button button-primary" type="button">Check</button>');
	        button.on('click', function (event){
	        	var inputValue = $(this).siblings('.card-input').val();
	        	var referenceValue = $(this).parent('.card')[0].dataset.id;
	        	var referenceName = $(this).parent('.card')[0].dataset.nick;
	        	if(inputValue === referenceValue){
	        		console.log('You\'re right!');
	        		//Set the card to solved, additionally give a flag for the congrats text marker.
	        		$(this).parent('.card')[0].dataset.solved = 'solved';
	        		$(this).parent('.card')[0].dataset.cong = 'cong';
	        		// I hate myself
	        		$(this).siblings('h4').next().html('Congratulations, You have found '+referenceName+'!').nextAll().css('display', 'none');

	        		//ID to send to server
	        		var thisID = $('#cards').data('id');

	        		$.ajax({
						type: 'GET',
						dataType: 'json',
						url: '../backend/score.php?id='+thisID+'&t_id='+referenceValue,
						success: function(msg) {
							console.log(msg);
							//unsolved: How many are still left to guess
							//count: How many times people requested cards
							if(msg.count === 1 && msg.unsolved === 0){
								var moarbutton = $('<button type="button" id="moar-button" class="four offset-by-four columns button-primary">Get more cards</button>');
								moarbutton.on('click', function (e){
									$('#cards').children('.card').remove();
									getCards(thisID);
								});
								moarbutton.appendTo($('#cards'))
							}

						}
					}).fail( function (error) {
						console.log('error', error)
					});
	        	} else {
	        		console.log('wrong guess');
	        		//I hate myself
	        		$(this).prev('.card-input').val('').attr('placeholder', 'Wrong guess!').css('border', '1px solid red');
	        		//TODO make a sad face, lock the card for a minute?
	        	}
	        })
	        input.appendTo(cards[i]);
	        button.appendTo(cards[i]);
	    } else if (cards[i].dataset.cong !== 'cong'){
	    	//enter congratulatory text ONLY if it doesn't have it.
	    	// This will in theory only happen on a reconnect, where new cards are formed from the server
        	var referenceName = $(cards[i]).data('nick');
	    	var solvedText = $('<p>Congratulations, You have found '+ referenceName +'!</p>');
	    	solvedText.appendTo(cards[i]);
	    }
	}
}
