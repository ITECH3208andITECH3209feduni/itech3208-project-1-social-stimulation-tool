import React, { useState } from "react";
import { Container, Heading, Box, SimpleGrid, Text, VStack } from "@chakra-ui/react";
import HighlightText from "../terms&conditions/HighlightText";

const GrantMeredithInfoUrl =
    "https://www.federation.edu.au/research/find-an-expert/grant-meredith/";
const videoUrl = "https://www.youtube.com/embed/ScMzIvxBSi4";

const VideoFallback = () => (
    <Box
        w="full"
        style={{ aspectRatio: "16/9" }}
        bg="gray.100"
        display="flex"
        flexDirection="column"
        alignItems="center"
        justifyContent="center"
        p={6}
        textAlign="center"
    >
        <Heading as="h4" size="md" color="gray.500" mb={3}>
            Video Unavailable
        </Heading>
        <Text color="gray.500">
            The tutorial video will be updated soon. Please check back later.
        </Text>
    </Box>
);

function TutorialPage() {
    const [hasError, setHasError] = useState(false);

    return (
        <Container mt={"100px"} spaceY={"50px"} maxW="container.xl" mb={"100px"}>
            <Heading
                color="brand.500"
                fontFamily="Sora"
                fontWeight={"bold"}
                fontSize={48}
                textAlign="center"
            >
                Getting Started with Scenari-Aid
            </Heading>

            <SimpleGrid columns={{ base: 1, lg: 2 }} gap={10} alignItems="center">
                <Box w="full" borderRadius="xl" overflow="hidden" boxShadow="2xl" bg="gray.50">
                    {videoUrl && !hasError ? (
                        <iframe
                            width="100%"
                            style={{ aspectRatio: "16/9" }}
                            src={videoUrl}
                            title="Scenari-Aid Tutorial"
                            frameBorder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowFullScreen
                            onError={() => setHasError(true)}
                        ></iframe>
                    ) : (
                        <VideoFallback />
                    )}
                </Box>

                <VStack alignSelf="start" align="flex-start" gap={5} textAlign={"left"}>
                    <Heading as="h3" size="lg" color="brand.500" fontFamily="Sora">
                        About This Tutorial
                    </Heading>
                    <HighlightText
                        fontSize="lg"
                        color="brand.500"
                        lineHeight="tall"
                        text="This tutorial video was created by Grant Meredith. It is designed to guide users through the various features of the Scenari-Aid website and demonstrate how to effectively use the platform to maximize your experience."
                        highlights={[{ text: "Grant Meredith", url: GrantMeredithInfoUrl }]}
                    />
                    <Text fontSize="lg" color="gray.600" lineHeight="tall">
                        Watch the video to learn step-by-step instructions on navigating our
                        scenarios, using our tools, and getting the most out of Scenari-Aid.
                    </Text>
                </VStack>
            </SimpleGrid>
        </Container>
    );
}

export default TutorialPage;
