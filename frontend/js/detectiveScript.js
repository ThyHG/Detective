console.log('hello');
var fake_questionnairre = {
	'q1': 'Are you healthy?',
	'q2': 'What is your favourite colour?',
	'q3': 'How do you know?',
	'q4': 'Do you like pointless questions?',
	'q5': 'What\'s the purpose of life?'
}

var init = function() {
	for (var key in fake_questionnairre) {
	    if (Object.prototype.hasOwnProperty.call(fake_questionnairre, key)) {
	        var val = fake_questionnairre[key];
	        $('#start-questionnairre').append('<label>'+ val +'<input type="text" name="'+key+'"></label>')
	    }
	}
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