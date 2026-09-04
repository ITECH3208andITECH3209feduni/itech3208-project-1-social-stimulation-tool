import { Avatar, Button, Card, HStack, Text, VStack } from "@chakra-ui/react";
import { useState } from "react";
import { MdVerified } from "react-icons/md";
import RatingBar from "../ratingbar/RatingBar";

function FeedbackCard({ feedback }) {
    const [isExpanded, setIsExpanded] = useState(false);

    const previewLength = 200;
    const content = feedback.content || "";
    const isLong = content.length > previewLength;

    const preview = isLong ? `${content.slice(0, previewLength).trimEnd()}...` : content;

    return (
        <Card.Root borderColor="gray.500">
            <Card.Body>
                <HStack gap={4}>
                    {/* MARK: - User Avatar */}
                    <Avatar.Root>
                        <Avatar.Image src={feedback.user?.avatar?.url} />
                    </Avatar.Root>

                    {/* MARK: - Username and Rating Bar */}
                    <VStack alignItems="start" gap={0}>
                        <HStack>
                            <Card.Title>{feedback.user?.username || "Anonymous"}</Card.Title>
                            <MdVerified color="green" />
                        </HStack>
                        <RatingBar rate={feedback.rating} />
                    </VStack>
                </HStack>

                {/* MARK: - User Rating Content */}
                <Text mt={4} fontSize="14px" color="gray.700" lineHeight="1.7">
                    {isExpanded ? content : preview}
                    {/* MARK: - Show More / Show Less Action */}
                    {!isExpanded && isLong && (
                        <Button
                            variant="plain"
                            size="xs"
                            color="brand.500"
                            h={1}
                            p={0}
                            ml={1}
                            verticalAlign="baseline"
                            fontWeight="semibold"
                            onClick={() => setIsExpanded(true)}
                        >
                            Read more
                        </Button>
                    )}
                    {isExpanded && isLong && (
                        <Button
                            variant="plain"
                            size="xs"
                            color="brand.500"
                            h={1}
                            p={0}
                            ml={1}
                            verticalAlign="baseline"
                            fontWeight="semibold"
                            onClick={() => setIsExpanded(false)}
                        >
                            Show less
                        </Button>
                    )}
                </Text>
            </Card.Body>
        </Card.Root>
    );
}

export default FeedbackCard;
