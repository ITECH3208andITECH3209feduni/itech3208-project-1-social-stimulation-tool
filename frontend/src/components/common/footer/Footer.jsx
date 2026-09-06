import { Link, VStack, Image, Flex, Text, HStack } from "@chakra-ui/react";
import { footerImg } from "@/assets";
import React from "react";
import HighlightText from "@/pages/terms&conditions/HighlightText";
import { useNavigate } from "react-router-dom";

const footerUrl =
    "https://federation.edu.au/schools/school-of-engineering-information-technology-and-physical-sciences/research/computational-science-and-mathematics/ciao/research-groups/technologies-for-empowering-people-for-participation-in-society-tepps";

function Footer() {
    const navigate = useNavigate();

    const goToTermsandConditions = () => navigate("/terms");

    return (
        <Flex
            as={"nav"}
            h={"80px"}
            py={"80px"}
            align={"center"}
            justify={"center"}
            gap={"6"}
            bg={"white"}
            color={"black"}
        >
            <VStack align={"center"}>
                <HStack>
                    <Text fontSize={"12px"} fontWeight={"semibold"}>
                        Copyright © 2026. Grant Meredith |
                    </Text>
                    <HighlightText
                        color="skyblue.500"
                        fontSize="12px"
                        text={
                            "Developed by Stimulation Tool Upgrade Group | Read our Terms & Conditions"
                        }
                        highlights={[
                            { text: "Terms & Conditions", action: goToTermsandConditions },
                        ]}
                    />
                </HStack>

                <Link href={footerUrl} target="_blank" rel="noopener noreferrer">
                    <Image
                        src={footerImg}
                        alt="Federation University"
                        h={"60px"}
                        cursor={"pointer"}
                    />
                </Link>
            </VStack>
        </Flex>
    );
}

export default Footer;
