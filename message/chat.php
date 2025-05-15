<?php
session_start();
require_once("../config/db.php");?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>WhatsApp Dark Theme UI</title>
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #121b22;
      color:rgb(0, 0, 0);
    }

    .container {
      display: flex;
      height: 100vh;
    }

    .sidebar {
      width: 30%;
      background-color:rgb(10, 100, 101);
      border-right: 1px solid #2a3942;
      display: flex;
      flex-direction: column;
    }

    .search-bar {
      padding: 10px;
      border-bottom: 1px solid #2a3942;
    }

    .search-bar input {
      width: 100%;
      padding: 8px;
      border-radius: 20px;
      border: none;
      background-color:rgb(216, 218, 219);
      color: #e9edef;
    }

    .chat-list {
      flex: 1;
      overflow-y: auto;
    }

    .chat-item {
      padding: 15px;
      border-bottom: 1px solid #2a3942;
      cursor: pointer;
      color: #e9edef;
    }

    .chat-item:hover {
      background-color: #2a3942;
    }

    .main {
      flex: 1;
      background-color:rgb(255, 255, 255);
      display: flex;
      flex-direction: column;
    }

    .chat-header {
      padding: 10px;
      background-color: rgb(216, 218, 219);;
      border-bottom: 1px solid #2a3942;
    }

    .chat-body {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .message {
      max-width: 60%;
      padding: 10px 14px;
      border-radius: 8px;
      color: #111b21;
      word-wrap: break-word;
    }

    .message.received {
      background-color: #202c33;
      align-self: flex-start;
      color: #e9edef;
    }

    .message.sent {
      background-color:rgb(10, 100, 101);
      align-self: flex-end;
      color: #e9edef;
    }

    .chat-footer {
      display: flex;
      padding: 10px;
      background-color: rgb(255, 255, 255);;
      border-top: 1px solid rgb(81, 85, 88);
      margin-bottom: 43px;
    }

    .chat-footer input {
      flex: 1;
      padding: 10px;
      border-radius: 20px;
      border: none;
      background-color:rgb(216, 218, 219);
      color: #e9edef;
      margin-right: 10px;
    }

    .chat-footer button {
      padding: 10px 15px;
      border: none;
      background-color: #00a884;
      color: white;
      border-radius: 50%;
      cursor: pointer;
    }

    .chat-footer button:hover {
      background-color: #008069;
    }
    .bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #a3eb93;
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 4px 0;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    z-index: 999;
  }
  
  .bottom-nav button {
    background: none;
    border: none;
    flex: 1;
    padding: 2px 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 12px;
    color: #000;
  }
  
  .bottom-nav .icon {
    font-size: 18px;
    line-height: 1;
    margin-bottom: 2px;
  }
  
  .bottom-nav .label {
    font-size: 10px;
    font-weight: 500;
  }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="search-bar">
        <h3>Penjual</h3>
        <input type="text" placeholder="Cari atau mulai chat"/>
      </div>
      <div class="chat-list">
        <div class="chat-item" style="background-color: #2a3942;">Kang Ujang</div> 
        <div class="chat-item">Pak Dudung</div>
        <div class="chat-item">Kang Ari</div>
        <div class="chat-item">Bang Aan</div>
        <div class="chat-item">Pak Ipul</div>
        <div class="chat-item">Pak Gendot (penjual ikan)</div> 
        <div class="chat-item">Mas Haris</div>
        <div class="chat-item">Bang Setia</div>
        <div class="chat-item">Mang Budi</div>
        <div class="chat-item">Om Zori</div>
        <div class="chat-item">Pak Aripin</div>
        <div class="chat-item">Mang Asep</div>
        
      </div>
    </div>
    <div class="main">
      <div class="chat-header">
        <strong>Kang Ujang</strong>
      </div>
      <div class="chat-body" id="chatBody">
        <div class="message received">Mas ada info ni, cabe lagi turun ni mas mau beli ga mas</div>
        <div class="message sent">ohh, boleh kang jang, berapa kang sekilo nya</div>
        <div class="message received">sekilo di kakang turun mas jadi 46 ribu sekilo nya mas</div>
        <div class="message sent">okee, mau kang 2kg yaa</div>
        <div class="message received">okee tunggu ya mas ini langsung otw di anter,gass  ngeng</div>
        <div class="message sent">okeee siap kang</div>
      </div>
      <div class="chat-footer">
        <input type="text" id="messageInput" placeholder="Ketik pesan"/>
        <button onclick="sendMessage()">➤</button>
      </div>
      <?php 
include('../navbar/bot_nav.php');
?>
    </div>
  </div>

  <script>
    function sendMessage() {
      const input = document.getElementById("messageInput");
      const chatBody = document.getElementById("chatBody");

      if (input.value.trim() !== "") {
        const message = document.createElement("div");
        message.classList.add("message", "sent");
        message.textContent = input.value;
        chatBody.appendChild(message);
        input.value = "";
        chatBody.scrollTop = chatBody.scrollHeight;
      }
    }
  </script>
</body>
</html>
