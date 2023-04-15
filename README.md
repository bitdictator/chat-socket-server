# Chat Socket Server

A simple chat socket server for your website.

## How does it work?

1. A user visits the conversation page on your website.
2. Upon loading, the client requests encrypted authentication details from your website for the socket server to verify the connection.
3. A web socket is created on the client side, which sends a connection request to the server, including the authentication details as a query parameter.
4. The socket server decrypts and validates the authentication details.
5. If valid, the server creates a new chat room (if it doesn't already exist) using the chat ID from the authentication details, and adds the user with their user ID and connection to the room.
6. When a second participant joins the conversation, they request a connection with their authentication details. If valid, they are connected to the existing room using their user ID and connection.
7. When a participant sends a message, the socket server identifies the room associated with the authenticated connection, saves the message to your website's database, and then forwards the message to the other participant in the room.
8. Upon receiving the message from the socket server, the client-side socket executes a method to display the live message in the conversation.

## TODO

-   Add code examples to README.md
-   Add deployment guide to README.md
