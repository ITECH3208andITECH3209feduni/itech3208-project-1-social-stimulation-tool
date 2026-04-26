import { landingPage, star2 } from "@/assets";
import {
    Box,
    Button,
    Flex,
    Float,
    Heading,
    HStack,
    Image,
    Text,
    VStack,
    Separator,
} from "@chakra-ui/react";

function GeneralInfor() {
    return (
        <HStack p="20px" gap={8} fontFamily="Sora">
            <VStack gap={2}>
                <Text color={"navy.500"} fontWeight={"bold"} fontSize={40}>
                    200+
                </Text>
                <Text color={"navy.500"} fontWeight={"regular"} fontSize={16}>
                    Active users
                </Text>
            </VStack>
            <Separator
                borderColor="gray.500"
                orientation={"vertical"}
                variant="solid"
                size={"md"}
                height={"50px"}
            />
            <VStack gap={2}>
                <Text color={"navy.500"} fontWeight={"bold"} fontSize={40}>
                    2,000+
                </Text>
                <Text color={"navy.500"} fontWeight={"regular"} fontSize={16}>
                    Scenario Videos
                </Text>
            </VStack>
            <Separator
                borderColor="gray.500"
                orientation={"vertical"}
                variant="solid"
                size={"md"}
                height={"50px"}
            />
            <VStack gap={2}>
                <Text color={"navy.500"} fontWeight={"bold"} fontSize={40}>
                    30,000+
                </Text>
                <Text color={"navy.500"} fontWeight={"regular"} fontSize={16}>
                    Happy Customers
                </Text>
            </VStack>
        </HStack>
    );
}

export default GeneralInfor;
