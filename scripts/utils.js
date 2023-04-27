const fs = require('fs');

function getCurrentVersion() {
	try {
		const packageJson = fs.readFileSync('../composer.json');
		const { version } = JSON.parse(packageJson);
	
		return version;
	} catch (error) {
		console.error(error);
		return null;
	}
}

module.exports = {
	getCurrentVersion
}
