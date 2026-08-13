import { Avatar, Card, HStack, VStack } from "@chakra-ui/react";
import { MdVerified } from "react-icons/md";
import RatingBar from "../ratingbar/RatingBar";

function FeedbackCard({ feedback }) {
    return (
        <Card.Root borderColor={"gray.500"}>
            <Card.Body>
                <HStack gap={4}>
                    <Avatar.Root>
                        <Avatar.Image src={feedback.user?.avatar?.url} />
                    </Avatar.Root>

                    <VStack alignItems={"start"} gap={"-1"}>
                        <HStack>
                            <Card.Title>{feedback.user?.username || "Anonymous"}</Card.Title>
                            <MdVerified color="green" />
                        </HStack>
                        <RatingBar rate={feedback.rating} />
                    </VStack>
                </HStack>
                <Card.Description>
                    "{feedback.content}"
                </Card.Description>
            </Card.Body>
        </Card.Root>
    );
}

export default FeedbackCard;
