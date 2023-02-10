C=php8.1
JAR=java -jar

SRC_FILE=parse.php
MY_TEST_FILE=./my_test/my_test.src
MY_OUT_FILE=my_test.out

PARSE_TESTS_PATH=../assignment/parse-only

OUT_DIR_PATH=./out
JAR_BIN_FILE=../jexamxml/jexamxml.jar
OPTIONS_FILE=../jexamxml/options

#run: clean
#	@$(C) $(SRC_FILE) < $(MY_TEST_FILE) > $(MY_OUT_FILE)
#	@cat $(MY_OUT_FILE)

run: clean
	@$(C) $(SRC_FILE) < $(PARSE_TESTS_PATH)/read_test.src > $(OUT_DIR_PATH)/read_test.xml
	@cat $(OUT_DIR_PATH)/read_test.xml
	@echo "\n"
	@$(JAR) $(JAR_BIN_FILE) $(OUT_DIR_PATH)/read_test.xml $(PARSE_TESTS_PATH)/read_test.out $(OUT_DIR_PATH)/delta.xml $(OPTIONS_FILE)

clean:
	@rm -rf $(MY_OUT_FILE)
	@rm -rf $(OUT_DIR_PATH)/*
