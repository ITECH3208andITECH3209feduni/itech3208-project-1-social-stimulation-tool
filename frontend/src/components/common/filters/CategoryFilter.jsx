import { categories } from "@/assets/index";
import { Button, Flex, Text, VStack } from "@chakra-ui/react";

function CategoryFilter() {
    return (
        <VStack align={"start"}>
            <Text color={"navy.500"} fontFamily={"Sora"} fontWeight={"medium"} fontSize={25}>
                Category
            </Text>
            <Flex wrap="wrap" gap={2}>
                {categories.map((category) => {
                    return (
                        <Button
                            key={category.key}
                            h={"40px"}
                            bg={category.selected ? "navy.500" : "gray.500"}
                            rounded={"full"}
                        >
                            {category.title}
                        </Button>
                    );
                })}
            </Flex>
        </VStack>
    );
}

export default CategoryFilter;
