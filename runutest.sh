source ~/.bashrc
rm -rf ./log/
rm -rf ./log_test/
phpunit ./utest/AllTests.php
