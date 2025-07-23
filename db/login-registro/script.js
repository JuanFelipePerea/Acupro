document
	.getElementById("loginForm")
	.addEventListener("submit", function (event) {
		event.preventDefault();

		const username = document.getElementById("username").value;
		const password = document.getElementById("password").value;

		// Aquí puedes agregar la lógica para validar el usuario y contraseña
		if (username === "usuario" && password === "contraseña") {
			alert("Inicio de sesión exitoso");
			// Redireccionar o realizar otras acciones
		} else {
			alert("Usuario o contraseña incorrectos");
		}
	});