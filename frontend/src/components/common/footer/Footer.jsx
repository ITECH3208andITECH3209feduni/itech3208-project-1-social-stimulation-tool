import { HStack, Text, VStack, Image, Flex } from "@chakra-ui/react";
import { Link } from "react-router-dom";
import { federationLogo } from "@/assets";
import React from "react";
import SocialBar from "../socialbar/SocialBar";

function Footer() {
    return (
        <Flex
            as={"nav"}
            h={"80px"}
            px={"100px"}
            py={"100px"}
            align={"center"}
            justify={"space-between"}
            gap={"6"}
            bg={"white"}
            color={"black"}
        >
            <Link to="/">
                <Image
                    src={federationLogo}
                    alt="Federation University"
                    h={"60px"}
                    cursor={"pointer"}
                />
            </Link>
            <VStack align={"start"}>
                <Text fontFamily={"Sora"} fontSize={16} fontWeight={"bold"}>
                    DOMESTIC
                </Text>
                <Text color={"navy.500"} fontFamily={"Sora"} fontSize={30} fontWeight={"bold"}>
                    1800 FED UNI (1800 333 864)
                </Text>
            </VStack>
            <VStack align={"start"}>
                <Text fontFamily={"Sora"} fontSize={16} fontWeight={"bold"}>
                    INTERNATIONAL
                </Text>
                <Text color={"navy.500"} fontFamily={"Sora"} fontSize={30} fontWeight={"bold"}>
                    Contact Us
                </Text>
            </VStack>
            <VStack align={"start"}>
                <Text fontFamily={"Sora"} fontSize={16} fontWeight={"bold"}>
                    FIND US
                </Text>
                <SocialBar />
            </VStack>
        </Flex>
    );
}

export default Footer;
