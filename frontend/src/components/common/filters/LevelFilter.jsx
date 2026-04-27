import { levels } from "@/assets/index";
import { Button, Flex, Text, VStack } from "@chakra-ui/react";

function LevelFilter() {
    return (
        <VStack align={"start"}>
            <Text color={"navy.500"} fontFamily={"Sora"} fontWeight={"medium"} fontSize={25}>
                Level
            </Text>
            <Flex wrap="wrap" gap={2}>
                {levels.map((level) => {
                    return (
                        <Button
                            key={level.key}
                            h={"40px"}
                            bg={level.selected ? "navy.500" : "gray.500"}
                            rounded={"full"}
                        >
                            {level.title}
                        </Button>
                    );
                })}
            </Flex>
        </VStack>
    );
}

export default LevelFilter;
