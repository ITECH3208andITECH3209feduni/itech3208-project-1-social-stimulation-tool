import { Grid, GridItem } from "@chakra-ui/react";
import FeedbackCard from "../cards/FeedbackCard";

const selectedFeedbacks = [
    {
        name: "Sarah M.",
        rate: 5,
        comment:
            "Scenari-Aid helped me prepare for my job interview in ways I never expected. The realistic scenarios gave me the confidence I needed to speak clearly and stay calm under pressure.",
    },
    {
        name: "James T.",
        rate: 4,
        comment:
            "I struggled with social anxiety for years. Practicing through these simulated conversations made a huge difference — I finally feel comfortable starting a conversation with strangers.",
    },
    {
        name: "Priya K.",
        rate: 5,
        comment:
            "The variety of scenarios is impressive. Whether it's a workplace conflict or a casual chat, every situation felt real and challenging. My communication skills have improved drastically.",
    },
];

function UsersFeedbackGrid() {
    return (
        <Grid gap={8} templateRows="1fr" templateColumns="repeat(3, 1fr)">
            {selectedFeedbacks.map((feedback, index) => (
                <GridItem key={index}>
                    <FeedbackCard
                        name={feedback.name}
                        rate={feedback.rate}
                        comment={feedback.comment}
                    />
                </GridItem>
            ))}
        </Grid>
    );
}

export default UsersFeedbackGrid;
