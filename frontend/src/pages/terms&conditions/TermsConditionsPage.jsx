import { Box, Heading, VStack } from "@chakra-ui/react";
import SectionCard from "./SectionCard";
const sections = [
    {
        id: "share",
        heading: "You Are Free To",
        title: "Share",
        color: "navy.500",
        items: [
            "You are free to distribute the URL of this website to friends, family and colleagues.",
        ],
    },
    {
        id: "attribution",
        heading: "Condition",
        title: "Attribution",
        color: "brand.500",
        items: [
            "You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).",
            "You must ensure Grant Meredith is attributed as the author.",
            "You must ensure that Federation University Australia is attributed as the owner of intellectual property rights. You must ensure that Telematics Trust, Australia is attributed as the funding agency.",
            "You must allow Federation University Australia to advertise that you are a user of the Scenari-Aid web site. User statistics and figures may be used in future research projects by Federation University Australia in a de-identified fashion and with permission from Federation University Australia's Human Ethics Committee.",
        ],
    },
    {
        id: "noncommercial",
        heading: "Condition",
        title: "Non-commercial",
        color: "brand.500",
        items: [
            "You may not use Scenari-Aid web site for commercial purposes.",
            "You are not allowed to profit directly from the contents and purpose of the Scenari-Aid web site.",
            "You are not allowed to charge access fees to the Scenari-Aid web site.",
        ],
    },
    {
        id: "research",
        heading: "Condition",
        title: "Research",
        color: "brand.500",
        items: [
            "You may use the Scenari-Aid web site for research purposes under the following conditions: Your research must be approved by an appropriately accredited Ethics Committee; You describe the purpose, aims and methodology of your project; You request permission from Grant Meredith.",
        ],
    },
    {
        id: "education",
        heading: "Condition",
        title: "Education",
        color: "brand.500",
        items: [
            "Scenari-Aid web site can be used for educational purposes under the following conditions: You are registered as an educational services provider with government authorities in your jurisdiction; You describe the context of the use of the Scenari-Aid web site; You request permission from Grant Meredith.",
        ],
    },
    {
        id: "noderivative",
        heading: "Condition",
        title: "No Derivative Works",
        color: "brand.500",
        items: ["You may not alter, transform, or build upon this work."],
    },
    {
        id: "waiver",
        heading: "Understanding",
        title: "Waiver",
        color: "navy.500",
        items: [
            "Any of the above conditions can be waived if you get permission from Grant Meredith.",
        ],
    },
    {
        id: "publicdomain",
        heading: "Understanding",
        title: "Public Domain",
        color: "navy.500",
        items: [
            "Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.",
        ],
    },
    {
        id: "otherrights",
        heading: "Understanding",
        title: "Other Rights",
        color: "navy.500",
        items: [
            "Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations.",
            "The author's moral rights.",
            "Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.",
        ],
    },
];

// Main page
import React from "react";

function TermsConditionsPage() {
    return (
        <Box>
            <Heading color="brand.500" fontFamily="Sora" fontWeight={"bold"} fontSize={48}>
                Terms and Conditions
            </Heading>
            <VStack gap={"8"} align={"stretch"}>
                {sections.map((s) => (
                    <SectionCard key={s.id} {...s} />
                ))}
            </VStack>
        </Box>
    );
}
export default TermsConditionsPage;
