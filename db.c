/**
* Table to store instructor's data
*/
struct Instructor{
	int instructor_id;				// primary key
	string user_name;					// LDAP userid in this case
	string name;					// Name of instructor
	string password;				// Will not be used here but can be used if the app has to be extended
};

/**
* Table to store The list of All quizzes
*/
struct Quiz
{
	int quiz_id;					// Primary Key
	int instructor_id;				// foreign key to instructor's table
	string couse_code; 				// Eg : CS101 , CS344 etc
	string quiz_description; 		// 2-3 lines of quiz description
};

/**
* Table to store The list of All Questions
*/
struct Questions
{
	int quiz_id;					// Foreign key to Quiz
	int question_no;				// Primary key along with the quiz Id
	float marks;					// Marks for this question
	int type;						// 1:MCQ single correct , 2: MCQ multi correct , 3: Integer type answer
									// 4 : Float type answer , 5 : string answer
	string question;					// The text of question
	string op1;						// Option1
	string op2;						// Option2
	string op3;						// Option3
	string op4;						// Option4
	string op5;						// Option5
	string op6;						// Option6
	// Note : This will provide to create questions with support of atmost 6 options
	string answer;					// single character in case of MCQ single character , array in case of MCQ multiple correct
									// int , float , string represented as string can be used accordingly
};

/**
* Table to store The list of All Responses; 1 per quiz per student
*/
struct Response{
	int quiz_id;					// Response from a particular quiz
	string student_roll;			// Roll No of student ; Will be sent using ldap in this case
	string student_name;			// Name of student; Obtained from Ldap
	string responses;				// Responses of all questions; In json Format
	int marks;						// Marks obtained by student
};