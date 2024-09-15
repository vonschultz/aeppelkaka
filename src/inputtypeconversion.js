export function convertFakePasswordInput () {
	const collection = document.getElementsByClassName("fake_password_input");

	for (let i = 0; i < collection.length; i++) {
		var input = collection[i];
		input.type = "text";
	}

	console.log('Updated fake password input fields to text.')
}
