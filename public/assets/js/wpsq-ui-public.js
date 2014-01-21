(function ( $ ) {
	"use strict";

	$(function () {


	// increment / decrement buttons for wpsq-questions-number

	var $questionsNumber = $( '#wpsq-questions-number' ),
		$maxQuestions = Number( $questionsNumber.data( 'max-questions' ) ),
		$minQuestions = Number( $questionsNumber.data( 'min-questions' ) );

	// insert buttons
	$questionsNumber
		.before( '<button class="wpsq-number-minus"><span class="wpsq-icon-minus"></span></button>' )
		.after( '<button class="wpsq-number-plus"><span class="wpsq-icon-plus"></span></button>' );

	$('.wpsq-number-plus').on( 'click', function(e){
		e.preventDefault();
		var $val = $questionsNumber.val();
		if( $val < $maxQuestions ){
			$val++;
			$questionsNumber.val( $val );
		}
		// console.log(quant + ', '+ max_quant_product);
		// console.log(typeof max_quant_product);
	} );

	$('.wpsq-number-minus').on( 'click', function(e){
		e.preventDefault();
		var $val = $questionsNumber.val();
		if( $val > $minQuestions ){
			$val--;
			$questionsNumber.val( $val );
		}
	} );

	$questionsNumber.keyup(function(){
		var $val = $questionsNumber.val();
		if( $val > $maxQuestions ){
			$questionsNumber.val( $maxQuestions );
		}
	}) 

	/* ==========================================================*/
	// BACKBONE OBJECTS											 //
	/* ==========================================================*/

		// cached objects
		var $controls = $('#wpsq-quiz-controls'),
			$wrapper = $('#wpsq-quiz-questions'),
			$typesSelector = $('#wpsq-types-select'),
			$loading = $('#wpsq-loading'),
			$questionsContainer = $('#wpsq-quiz-questions'),
			$questionsOrder = [ 'a','b','c','d','e' ];

		// the Quiz global namespace
		var quiz = quiz || {};

	// LOAD TEMPLATES FOR QUESTIONS AND RESULTS

		// load templates for questions and for results
		$.get( wpsq_vars.tplQuestion, function( content ){
			quiz.tplQuestion = content;
		} );
		$.get( wpsq_vars.tplResults, function( content ){
			quiz.tplResult = content;
		} );

	// QUIZ OBJECTS =============================================//

		// a model to retrieve the Quiz data, like number of questions answered, right / wrong answers, etc.
		quiz.Data = Backbone.Model.extend({
			defaults: {
				questions_total: 0,
				questions_answered: 0,
				questions_correct: 0,
			}
		});

		// a Collection of questions
		quiz.Questions = Backbone.Collection.extend({
			url: wpsq_vars.json,
			initialize: function(){
			  // this.bindAll(this);
			  this.setElement(this.at(0));
			},
			comparator: function(model) {
			  return model.get("id");
			},
			getElement: function() {
			  return this.currentElement;
			},
			setElement: function(model) {
			  this.currentElement = model;
			},
			next: function (){
			  this.setElement(this.at(this.indexOf(this.getElement()) + 1));
			  return this;
			},
			prev: function() {
			  this.setElement(this.at(this.indexOf(this.getElement()) - 1));
			  return this;
			}
		});

		// the main view of the Quiz
		quiz.QuizView = Backbone.View.extend({
			el: '#wpsq-quizzes-ui',
			events: {
				'click #wpsq-start-quiz' : 'startQuiz',
				'click #wpsq-quiz-next' : 'nextQuestion',
				'click #wpsq-quiz-prev' : 'previousQuestion',
				'click #wpsq-quiz-finish' : 'finishQuiz',
				'click #wpsq-results-answers-toggle' : 'toggleResults'
			},
			initialize: function(){
				this.listenTo( this.collection, 'reset', this.quizInit );
				this.prevBtn = $controls.find( '#wpsq-quiz-prev' );
				this.nextBtn = $controls.find( '#wpsq-quiz-next' );
				this.lastBtn = $controls.find( '#wpsq-quiz-finish' );
			},
			render: function( model ){
				// create a view for each question and append it to the quiz view
				var questionWrapper = new quiz.QuestionView({ model: model });

				$wrapper.html( questionWrapper.render().el );

			},
			startQuiz: function(e){
				// $(e.currentTarget).preventDefault;

				var self = this;

				// get the number of questions for the quiz
				var questions_num = $questionsNumber.val();

				// get an array of the selected courses
				var courses_ids = $('.wpsq-type-checkbox:checked').map(function() {
				  return this.value;
				}).get();

				if( courses_ids.length < 1 ){
					// if none of the courses was selected, then alert
					alert( wpsq_labels.noterms );
				}else if( !$.isNumeric( questions_num ) ){
					// if the input field with number of questions isNaN, then alert
					alert( wpsq_labels.isnan );
				} else {

					$typesSelector.hide();
					$loading.show();

					// build the params variable
					var params = {
						action: 'get_questions',
						courses: courses_ids,
						number: questions_num
					}

					this.collection.fetch(
						{ 
							data: $.param(params),
		                    success: (function () {

		                    	quiz.currentData = new quiz.Data();
								quiz.currentData.set({ 
									questions_total: Number( questions_num ),
									labels: wpsq_labels 
								});

		                        self.quizInit();
		                    }),
		                    // error:(function (e) {
		                    //     console.log(' Service request failure: ' + e);
		                    // }),
		                    // complete:(function (e) {
		                    //     console.log(' Service request completed ');

		                    // })
						}
					);

				}

			},
			quizInit: function(){

				// reset the currentElement from collection
				this.collection.setElement( this.collection.at(0) );

				// initialize the quiz
				$controls.show();
				$typesSelector.remove();

				var question = this.collection.first();
				this.render( question );
			},
			nextQuestion: function(){

				this.updateQuestion();

				var question = this.collection.next().getElement();

				// check if next question is the last
				if ( question = this.collection.last() ){
					this.nextBtn.hide();
					this.lastBtn.show();
				}

				this.prevBtn.show();

				this.render( question );
			},
			previousQuestion: function(){
				var question = this.collection.prev().getElement(),
					current = this.collection.getElement();

				// check if previous question isn't the first and the next isn't the last
				this.lastBtn.hide();
				this.nextBtn.show();

				if( question = this.collection.first() ){
					this.prevBtn.hide();
				} else {
					this.prevBtn.show();
				}

				this.render( question );
			},
			updateQuestion: function() {

				var current = this.collection.getElement(),
					has_answer = current.get( 'answer_selected' ),
					answered = current.get( 'answered' );

				// checks if this question was not answered before, and has a selected answer 
				if( !answered && has_answer ){
					quiz.currentData.set({ questions_answered: quiz.currentData.get( 'questions_answered' ) + 1 });
					current.set({ answered: true });
				}

			},
			finishQuiz: function(){

				// update last question
				this.updateQuestion();

				// variable to count total correct answers
				var total_correct = 0,
					answersArray = [];

				// iterate over each question and update total_correct
				this.collection.each( function( question, key ){

					var qSelected = question.get( 'selected' ) ? $questionsOrder[ question.get( 'selected' ) ] : wpsq_labels.none;

					// console.log( question );
					var thisAnswer = {
						selected: qSelected,
						correct: $questionsOrder[ question.get( 'answer_correct' ) ]
					};

					var correct_answer = question.get( 'answer_selected' );

					// if the answer is correct, update the count
					if( correct_answer == 'yes' ){
						total_correct = total_correct + 1;
						thisAnswer.correct_answer = "yes";
					} else if( correct_answer == 'no' ) {
						thisAnswer.correct_answer = "no";
					} else {
						thisAnswer.correct_answer = "none";
					}

					answersArray.push( thisAnswer );

				});

				// finally, quiz data with the final result for the correct answers 
				quiz.currentData.set({ 
					questions_correct: total_correct,
					questionsResults: answersArray
				});

				// render the results of the quiz
				this.renderResults();			
			},
			renderResults: function(){

				$controls.html( '<button class="wpsq-button" id="wpsq-new-quiz">' + wpsq_labels.newQuiz + '</button>' );		
				$wrapper.html( _.template( quiz.tplResult, quiz.currentData.toJSON() ) );

				$('#wpsq-results-answers-list').hide();
			},
			toggleResults: function(e){
				$(e.currentTarget).toggleClass('active');
				$('#wpsq-results-answers-list').slideToggle();
			}

		});

	// QUESTION OBJECTS =========================================//

		// a question model
		quiz.Question = Backbone.Model.extend({
			defaults: {
				selected: null
			}
		});

		// the view of each question
		quiz.QuestionView = Backbone.View.extend({
			tagName: 'div',
			className: 'wpsq-question-body',
			events: {
				// insert events from the question interaction here
				'click .wpsq-answer' : 'checkAnswer',
			},
			initialize: function(){

				this.template = _.template( quiz.tplQuestion );
				this.render();

			},
			render: function(){
				this.$el.html( this.template(
					{
						index: quiz.questions.indexOf( this.model ) + 1,
						label: wpsq_labels.question,
						courses: this.model.get( 'courses' ),
						title: this.model.get( 'title' ),
						question: this.model.get( 'question' ),
						answers: this.model.get( 'answers' ),
						selected: this.model.get( 'selected' )
					}
				) );

				return this;
			},
			checkAnswer: function(e){

				var $current = $(e.currentTarget),
					selected_new = $current.attr( 'data-answer-num' );

				this.model.set({ selected: selected_new });

				$('.wpsq-answer').removeClass( 'selected' );
				$current.addClass( 'selected' );


				// set the result of the answer selected
				if( selected_new == this.model.get( 'answer_correct' ) ){
					this.model.set({ answer_selected: 'yes' });	
				} else {
					this.model.set({ answer_selected: 'no' });
				}
			}
		});

	// INITIALIZING OBJECTS

		quiz.questions = new quiz.Questions({ model: quiz.Question });

		quiz.TheQuiz = new quiz.QuizView({ collection: quiz.questions });

	});

}(jQuery));