from configparser import ConfigParser
# read database configuration file, return dictionary with database configuration
def readDbConfig(filename='config.ini',section='mysql'):
	# create parser and read ini configuration file
	parser = ConfigParser()
	parser.read(filename)
	# get section
	db = {}
	if parser.has_section(section):
		items = parser.items(section)
		for item in items:
			db[item[0]] = item[1]
	else:
		raise Exception('{0} not found in the {1} file'.format(section,filename))

	# return database after fetching paramaters
	return db