import { ReplaySubject } from 'rxjs';
/**
 * EndlessReplaySubject extension of Rx.ReplaySubject.
 * This is pretty hacky, but so far I'd found no better way of having
 * Subjects that do no close on multicasted stream completion and on multiple errors.
 * For documentation refer to
 * [ReplaySubject docs](@link https://github.com/Reactive-Extensions/RxJS/blob/master/doc/api/subjects/replaysubject.md).
 * The only difference is that EndlessReplaySubject never triggers '.complete()' and
 * does not closes observers on errors (thus allowing to continuously dispatch them).
 */
export declare class EndlessReplaySubject<T> extends ReplaySubject<T> {
    /**
     * Dummy method override to prevent execution and Rx.Observable completion
     */
    complete(): void;
    /**
     * Override of error method that prevents stopping that Rx.Observer
     * @param error  - Error to be dispatched
     */
    error(error: any): void;
}
/**
 * Converts topic to search regex
 * @param  topic   Topic name
 * @return         Search regex
 */
export declare const topicToRegex: (topic: string) => string;
/**
 * Compares given topic with existing topic
 * @param  topic         Topic name
 * @param  existingTopic Topic name to compare to
 * @return Whether topic is included in existingTopic
 * @example
 * should(compareTopics('test.one.two', 'test.#')).equal(true);
 */
export declare const compareTopics: (topic: string, existingTopic: string) => boolean;
/**
 * Find a specific subject by given name
 * @param  subjects    Array of subjects to search in
 * @param  name        Name to search for
 * @return Found subject or void
 */
export declare const findSubjectByName: (subjects: any[], name: string) => any;
