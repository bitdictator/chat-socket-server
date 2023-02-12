# Chat Socket Server

A simple chat socket server for your website.

## How does it work?

1. The user opens a conversation page on your website.
2. When the page loads, the client requests from your website the encrypted authentication details necessary for the socket server to authenticate the connection.
3. Then a web socket is created on the client side which requests a connection to this server with the authentication details as a query parameter.
4. The socket server decrypts the authentication details and validates them.
5. The socket server creates a new room (If it does not exist) with the chat ID that is given in the auth details and inserts the user with the given user ID to the room.
6. When the second conversation participant opens the same conversation on your website, he requests the connection with the auth details, if details are valid then he connects to the existing room with his user ID.
7. When the client sends a message to the socket server, the socket server saves the message to the database of your website and sends the message to the participant in the same room by using his connection.
8. Once the client side socket receives a message from this socket server, it runs a method that displays the live message in the conversation.

## TODO

* Add code examples to README.md
* Add deployment guide to README.md
