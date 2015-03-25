CS101	#This is the Course Name

# Put the Quiz basic detais in between the three quotes
'''
This is a demo Quiz template
This is Line 2 of Description
'''

500 		# Total time of quiz in seconds

# Dont delete the 5 parameters below , only put yes / no

yes 		# Authenticated Quiz ?
yes			# Show marks at end ?
no 			# Show Answers at End ?
no 			# Shuffle questions (Enabling this will not display question numbers)
yes 		# Shuffle Options in question

# Note : This part is for information about this markdown and you need not to change it
# Anything after a hash # will be ignored
# You must not delete any uncommented part
# A line (**********************) is question demarkator

*****************************

1 			# This is question number. It can be alphanumeric
2.5			# Marks for this question

# Put the Question description
'''
What is the Capital of India ?
You Must Know it
'''
5 			# Type of Question (5 is fill in string answer)
# 1 -> Single Choice
# 2 -> Multiple Choice 
# 3 -> Integer Answer
# 4 -> Float Answer
# 5 -> String Answer

# Put all options in new lines
# Note string match is case sensitive , to make it insenstitve fill options in all cases
# For correction any one of them will be macthed

'''
Delhi
New Delhi
'''


*******************************

2.a 		# This is question number. It can be alphanumeric
3.5 		# Marks for this question

# question description
'''
What is 1 + 1 ? 
'''
3 			# Type of Question (3 is Integer Answer)
# 1 -> Single Choice
# 2 -> Multiple Choice 
# 3 -> Integer Answer
# 4 -> Float Answer
# 5 -> String Answer

# Only first option will be considered in case of integer answer
'''
2
'''

*****************************

2.b 		# This is question number. It can be alphanumeric
3.5 		# Marks for this question
#question description
'''
Who is called as father of Nation ?
'''
1 			# Type of Question (1 is MCQ single Correct)
# 1 -> Single Choice
# 2 -> Multiple Choice 
# 3 -> Integer Answer
# 4 -> Float Answer
# 5 -> String Answer

# You can add as many answer as you want
# Add a star (*) followed by space in correct answer
'''
Jawahar Lal Nehru
* Mahatma Gandhi
Atal Bihari Vajpayee
Arvind Kejriwal
'''

*****************************

3 			# This is question number. It can be alphanumeric
4.5 		# Marks for this question
#question description
'''
Chose the correct answers : 
'''
2 			# Type of Question (2 is MCQ multiple Correct)
# 1 -> Single Choice
# 2 -> Multiple Choice 
# 3 -> Integer Answer
# 4 -> Float Answer
# 5 -> String Answer

# You can add as many answer as you want
# Add a star (*) followed by space in all correct answers

'''
* Correct Answer
Wrong Answer
* Correct Answer
Wrong Answer
'''

******************************

4 			# This is question number. It can be alphanumeric
6 			# Marks for this question
#question description
'''
What is value of PI ? 
'''
4 			# Type of Question (4 is Float Answer)
# 1 -> Single Choice
# 2 -> Multiple Choice 
# 3 -> Integer Answer
# 4 -> Float Answer
# 5 -> String Answer

# For answer you have to input two numbers and correct answer will be a<=ans<=b
'''
3.14 # Lower range of Input
3.15 # Upper Range (Answer will be checked bounding between them)
'''

*******************************