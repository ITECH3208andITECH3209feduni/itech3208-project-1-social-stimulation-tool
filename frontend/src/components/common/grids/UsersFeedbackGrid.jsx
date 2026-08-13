import { Grid, GridItem } from "@chakra-ui/react";
import FeedbackCard from "../cards/FeedbackCard";

function UsersFeedbackGrid({ feedbacks }) {
    return (
        <Grid gap={8} templateColumns="repeat(3, 1fr)">
            {feedbacks.map((feedback) => (
                <GridItem key={feedback.id}>
                    <FeedbackCard feedback={feedback} />
                </GridItem>
            ))}
        </Grid>
    );
}

export default UsersFeedbackGrid;
