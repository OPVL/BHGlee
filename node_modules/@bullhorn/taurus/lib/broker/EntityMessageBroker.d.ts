import { Observable, Subject } from 'rxjs';
export declare class EntityMessageBroker {
    private static _instance;
    private readonly subjects;
    /**
     * Message bus
     */
    private readonly messageBus;
    /**
     * Permanent message bus stream as Observable
     */
    private readonly messageStream;
    constructor();
    static getInstance(): EntityMessageBroker;
    /**
     * Returns EndlessSubject representing given topic
     * @param  event           Topic Event
     * @example
     * const broker = EntityMessageBroker.getInstance();
     * const subject = broker.emit('test.topic', data);
     */
    emit(topic: string, data?: any): Subject<any>;
    /**
     * Returns EndlessSubject representing given topic
     * @param  name           Topic name
     * @example
     * const broker = EntityMessageBroker.getInstance();
     * const subject = broker.subject('test.topic');
     */
    subject(name: any): Subject<any>;
    /**
     * Get an Observable for specific set of topics
     * @param  name        Topic name / pattern
     * @return Rx.Observable for given set of topics
     * @example
     * const broker = EntityMessageBroker.getInstance();
     * broker.on('Candidate.child_added')
     *       .subscribe((res) => { // default Observable subscription
     *            // handle results
     *       });
     */
    on(name: any): Observable<any>;
}
