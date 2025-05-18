const input = document.querySelector("input")
const send = document.querySelector("button")
const chatContainer = document.querySelector(".chats")

send.onclick = () => {
  if (input.value) {
    const message = `
            <div class="message">
                <div>
                    ${input.value}
                </div>
            </div>
        `

    chatContainer.innerHTML += message
    scrollDown()
    bot(input.value)
    input.value = null
  }
}


input.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault()
    send.click()
  }
})


function scrollDown() {
  chatContainer.scrollTop = chatContainer.scrollHeight
}


function bot(question) {
  setTimeout(() => {
    chatContainer.innerHTML += `
            <div class="message response">
                <div>
                    <img src="../assets/icons/fade-stagger-circles.svg" alt="preloader" height="40px" >
                </div>
            </div>
        `
    scrollDown()
  }, 1000)

 
  fetch("../../backend/chatbot.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      question: question,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok")
      }
      return response.json()
    })
    .then((data) => {
      const loadingMessage = document.querySelector(".message.response:last-child")
      if (loadingMessage) {
        loadingMessage.remove()
      }

      if (data.reply) {
        chatContainer.innerHTML += `
                <div class="message response">
                    <div>
                        ${data.reply}
                    </div>
                </div>
            `
        scrollDown()
      } else if (data.error) {
        chatContainer.innerHTML += `
                <div class="message response">
                    <div>
                        Sorry, there was an error: ${data.error}
                    </div>
                </div>
            `
        scrollDown()
      }

      console.log("Response data:", data)
    })
    .catch((error) => {
      const loadingMessage = document.querySelector(".message.response:last-child")
      if (loadingMessage) {
        loadingMessage.remove()
      }

      chatContainer.innerHTML += `
            <div class="message response">
                <div>
                    Sorry, there was an error connecting to the server.
                </div>
            </div>
        `
      scrollDown()
      console.error("Error:", error)
    })
}

document.querySelector(".chatbot-float").addEventListener("click", () => {
  window.location.href = "../html/chatbot.html"
})
