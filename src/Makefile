C=php8.1
SRC_FILE=parse.php
MY_TEST_FILE=./my_test/my_test.src
MY_OUT_FILE=my_test.out

PARSE_TESTS_PATH=../assignment/parse-only

run: clean
	@$(C) $(SRC_FILE) < $(MY_TEST_FILE) > $(MY_OUT_FILE)
	@cat $(MY_OUT_FILE)

#parse: clean
#	@$(C) $(SRC_FILE) < $(PARSE_TESTS_PATH)/read_test.src > read_test.your_out
#	@echo $? > read_test.your_rc
#	@diff -su read_test.your_rc read_test.rc

clean:
	@rm -rf $(MY_OUT_FILE)
