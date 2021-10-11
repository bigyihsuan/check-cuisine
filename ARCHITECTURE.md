# ARCHITECTURE

## Front End
* User interacts with a "public" interface via a web page
* Web page sends requests to an Apache2 server
* Apache2 server converts the user input to a request
* Apache2 server converts backend response to additional webpage responses
* Apache2 server sends and receives messages from RabbitMQ server

## RabbitMQ
* Inter-end messaging system
* All ends (front, back, data) pass messages through queues on RabbitMQ server
* RabbitMQ passively listens and relays messages going in and out of the queues
* Queues
* * front-to-back
* * back-to-data
* * data-to-back
* * back-to-front

## Back End
* Handles converting user input into database queries
* PHP script listening to the front- and data-queues
* Inputs from the front-queue gets converted into a database query, and query is sent to the database via the data-queue
* Database responses arrive from the data-queue, converted into information for the front-end
* Information gets sent to the front-end via front-queue

## Database
* Stores and accesses the data of the app
* Receives queries from backend via back-queue
* Processes queries, and returns response to back-queue
* PHP script listening to back-queue

# Check Quisine Features
* https://trello.com/b/GxevaiEW/todo