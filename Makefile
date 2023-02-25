C=php8.1
JAR=java -jar

SRC_FILE=parse.php

ZIP_FILE=xduric06.zip

PARSE_TESTS_PATH=./assignment/parse-only

OUT_DIR_PATH=./out
JAR_BIN_FILE=./jexamxml/jexamxml.jar
OPTIONS_FILE=./jexamxml/options

run: clean
	@$(C) $(SRC_FILE) < $(PARSE_TESTS_PATH)/read_test.src > $(OUT_DIR_PATH)/read_test.xml
	@cat $(OUT_DIR_PATH)/read_test.xml
	@echo "\n"
	@$(JAR) $(JAR_BIN_FILE) $(OUT_DIR_PATH)/read_test.xml $(PARSE_TESTS_PATH)/read_test.out $(OUT_DIR_PATH)/delta.xml $(OPTIONS_FILE)

test: clean
	@$(C) test.php

zip: clean
	@zip -r $(ZIP_FILE) $(SRC_FILE) php_libs/ doc_imgs/ readme1.md rozsireni
	@echo y | ./assignment/is_it_ok.sh $(ZIP_FILE) ./assignment/tmp 1
clean:
	@rm -rf $(MY_OUT_FILE)
	@rm -rf $(OUT_DIR_PATH)/*
	@rm -rf ./tmp
	@rm -rf $(ZIP_FILE)