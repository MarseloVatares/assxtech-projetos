// Configure aqui o número da empresa: código do país + DDD + número, somente dígitos.
const WHATSAPP_NUMBER = "5591999999999";

document.getElementById("year").textContent = new Date().getFullYear();

document.getElementById("send-whatsapp").addEventListener("click", () => {
  const name = document.getElementById("name").value.trim();
  const topic = document.getElementById("topic").value;
  const details = document.getElementById("details").value.trim();
  const error = document.getElementById("error");
  error.textContent = "";

  if (!name) {
    error.textContent = "Informe seu nome para continuar.";
    document.getElementById("name").focus();
    return;
  }
  if (!topic) {
    error.textContent = "Selecione o assunto do atendimento.";
    document.getElementById("topic").focus();
    return;
  }
  if (!/^\d{12,13}$/.test(WHATSAPP_NUMBER)) {
    error.textContent = "Configure o número de WhatsApp da empresa no arquivo script.js.";
    return;
  }

  const message = `Olá! Meu nome é ${name}. Assunto: ${topic}.${details ? `\nMensagem: ${details}` : ""}`;
  const url = `https://wa.me/${WHATSAPP_NUMBER}?text=${encodeURIComponent(message)}`;
  window.open(url, "_blank", "noopener,noreferrer");
});

document.getElementById("floating-whatsapp").addEventListener("click", (event) => {
  event.preventDefault();
  document.getElementById("contato").scrollIntoView({ behavior: "smooth" });
});