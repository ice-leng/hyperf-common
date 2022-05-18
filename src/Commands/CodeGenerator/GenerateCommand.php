<?php

declare(strict_types=1);

namespace Lengbin\Hyperf\Common\Commands\CodeGenerator;

use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\ControllerGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\DaoGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\DaoInterfaceGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\ErrorGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\LogicGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\ModelGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorCondition;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorCreateData;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorCreateRequest;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorDetailRequest;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorListRequest;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorListSearch;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorModifyData;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorModifyRequest;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorRemoveRequest;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorRemoveSearch;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Request\GeneratorSearch;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Response\GeneratorDetailResponse;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Response\GeneratorListItem;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\Response\GeneratorListResponse;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\ServiceGenerator;
use Lengbin\Hyperf\Common\Commands\CodeGenerator\Generator\ServiceInterfaceGenerator;
use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Dag\Dag;
use Hyperf\Dag\Vertex;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

/**x
 * @Command
 */
#[Command]
class GenerateCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('gen:code');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Generate code file Command');

        $this->addOption('path', 'p', InputOption::VALUE_REQUIRED, '路径', '/app');
        $this->addOption('path_version', 'pv', InputOption::VALUE_REQUIRED, '版本', 'v1');
        $this->addOption('pool', 'P', InputOption::VALUE_REQUIRED, '数据连接池', 'default');
        $this->addOption('table', 't', InputOption::VALUE_OPTIONAL, '表');
        $this->addOption('url', 'u', InputOption::VALUE_REQUIRED, '请求url前缀', '/api');
        $this->addOption('applications', 'a', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, '应用端', []);
        $this->addOption('for_table_ddd', 'ddd', InputOption::VALUE_OPTIONAL, '根据表名区分模块');
    }

    public function handle()
    {
        $this->line('代码自动生成工具启动', 'info');

        $applications = $this->input->getOption('applications');
        if (empty($applications)) {
            $applications = config('generate.applications');
        }

        if (empty($applications)) {
            $this->alert('请设置应用端');
            return;
        }

        $config = new GeneratorConfig();
        $config->applications = $applications;
        $config->path = $this->input->getOption('path');
        $config->version = $this->input->getOption('path_version');
        $config->url = $this->input->getOption('url');

        $pool = $this->input->getOption('pool');
        $table = $this->input->getOption('table');
        $ddd = $this->input->getOption('for_table_ddd');
        if (is_null($ddd)) {
            $ddd = config('generate.for_table_ddd');
        }

        $this->process($config, $pool, $table, $ddd);

        $this->line('代码自动生成工具完成', 'info');
    }

    protected function getListRequest(array $condition): Vertex
    {
        $requestCondition = Vertex::of(new GeneratorCondition($condition), 'requestCondition');
        $requestListSearch = Vertex::of(new GeneratorListSearch($condition), 'requestListSearch');
        $requestList = Vertex::of(new GeneratorListRequest($condition), 'requestList');
        $dagRequestList = new Dag();
        $dagRequestList->addVertex($requestCondition)
            ->addVertex($requestListSearch)
            ->addVertex($requestList)
            ->addEdge($requestCondition, $requestList)
            ->addEdge($requestListSearch, $requestList);
        return Vertex::of($dagRequestList, 'entity_list');
    }

    protected function createRequest(array $condition): Vertex
    {
        $requestCreateData = Vertex::of(new GeneratorCreateData($condition), 'requestCreateData');
        $requestCreate = Vertex::of(new GeneratorCreateRequest($condition), 'requestCreate');
        $requestCondition = Vertex::of(new GeneratorCondition($condition), 'requestCondition');
        $requestSearch = Vertex::of(new GeneratorSearch($condition), 'requestSearch');

        $dagRequestCreate = new Dag();
        $dagRequestCreate->addVertex($requestCreateData)
            ->addVertex($requestCreate)
            ->addVertex($requestCondition)
            ->addVertex($requestSearch)
            ->addEdge($requestSearch, $requestCreate)
            ->addEdge($requestCreateData, $requestCreate)
            ->addEdge($requestCondition, $requestCreate);
        return Vertex::of($dagRequestCreate, 'entity_create');
    }

    protected function modifyRequest(array $condition): Vertex
    {
        $requestSearch = Vertex::of(new GeneratorSearch($condition), 'requestSearch');
        $requestModifyData = Vertex::of(new GeneratorModifyData($condition), 'requestModifyData');
        $requestModify = Vertex::of(new GeneratorModifyRequest($condition), 'requestModify');
        $requestCondition = Vertex::of(new GeneratorCondition($condition), 'requestCondition');

        $dagRequestModify = new Dag();
        $dagRequestModify->addVertex($requestSearch)
            ->addVertex($requestModifyData)
            ->addVertex($requestModify)
            ->addVertex($requestCondition)
            ->addEdge($requestCondition, $requestModify)
            ->addEdge($requestSearch, $requestModify)
            ->addEdge($requestModifyData, $requestModify);
        return Vertex::of($dagRequestModify, 'entity_modify');
    }

    protected function detailRequest(array $condition): Vertex
    {
        $requestCondition = Vertex::of(new GeneratorCondition($condition), 'requestCondition');
        $requestSearch = Vertex::of(new GeneratorSearch($condition), 'requestSearch');
        $requestDetail = Vertex::of(new GeneratorDetailRequest($condition), 'requestDetail');

        $dagRequestDetail = new Dag();
        $dagRequestDetail->addVertex($requestCondition)
            ->addVertex($requestSearch)
            ->addVertex($requestDetail)
            ->addEdge($requestSearch, $requestDetail)
            ->addEdge($requestCondition, $requestDetail);
        return Vertex::of($dagRequestDetail, 'entity_detail');
    }

    protected function removeRequest(array $condition): Vertex
    {
        $requestSearch = Vertex::of(new GeneratorSearch($condition), 'requestSearch');
        $requestRemoveSearch = Vertex::of(new GeneratorRemoveSearch($condition), 'requestRemoveSearch');
        $requestRemove = Vertex::of(new GeneratorRemoveRequest($condition), 'requestRemove');

        $dagRequestRemove = new Dag();
        $dagRequestRemove->addVertex($requestRemove)
            ->addVertex($requestSearch)
            ->addVertex($requestRemoveSearch)
            ->addEdge($requestSearch, $requestRemoveSearch)
            ->addEdge($requestRemoveSearch, $requestRemove);
        return Vertex::of($dagRequestRemove, 'entity_remove');
    }

    protected function getListResponse(array $condition): Vertex
    {
        $responseListItem = Vertex::of(new GeneratorListItem($condition), 'responseListItem');
        $responseList = Vertex::of(new GeneratorListResponse($condition), 'responseList');

        $dagResponseList = new Dag();
        $dagResponseList->addVertex($responseList)
            ->addVertex($responseListItem)
            ->addEdge($responseListItem, $responseList);
        return Vertex::of($dagResponseList, 'entity_list_response');
    }

    protected function detailResponse(array $condition): Vertex
    {
        $responseListItem = Vertex::of(new GeneratorListItem($condition), 'responseListItem');
        $dagResponseDetail = new Dag();
        $responseDetail = Vertex::of(new GeneratorDetailResponse($condition), 'responseDetail');
        $dagResponseDetail->addVertex($responseDetail)
            ->addVertex($responseListItem)
            ->addEdge($responseListItem, $responseDetail);
        return Vertex::of($dagResponseDetail, 'entity_item');
    }

    public function process(GeneratorConfig $config, string $pool = 'default', ?string $table = null, bool $ddd = false): void
    {
        // model 生成
        $models = (new ModelGenerator($this->container, $ddd))->generate($pool, $table);
        $modules = config('generate.modules', []);
        if (empty($modules)) {
            foreach ($models as $model) {
                if (!in_array($model->module, $modules)) {
                    $modules[] = $model->module;
                }
            }
        }

        foreach ($models as $model) {
            $condition = [
                'modelInfo' => $model,
                'config' => $config
            ];
            $getListRequest = $this->getListRequest($condition);
            $createRequest = $this->createRequest($condition);
            $modifyRequest = $this->modifyRequest($condition);
            $detailRequest = $this->detailRequest($condition);
            $removeRequest = $this->removeRequest($condition);

            $getListResponse = $this->getListResponse($condition);
            $detailResponse = $this->detailResponse($condition);

            $dag = new Dag();
            $dao = Vertex::of(new DaoGenerator($condition), 'dao');
            $daoInterface = Vertex::of(new DaoInterfaceGenerator($condition), 'daoInterface');
            $serviceInterface = Vertex::of(new ServiceInterfaceGenerator($condition), 'serviceInterface');
            $service = Vertex::of(new ServiceGenerator($condition), 'service');
            $error = Vertex::of(new ErrorGenerator(array_merge($condition, [
                'moduleIndex' => array_search($model->module, $modules) + 1
            ])), 'error');
            $logic = Vertex::of(new LogicGenerator($condition), 'logic');
            $controller = Vertex::of(new ControllerGenerator($condition), 'controller');

            $dag->addVertex($dao)
                ->addVertex($daoInterface)
                ->addVertex($error)
                ->addVertex($serviceInterface)
                ->addVertex($service)
                ->addVertex($logic)
                ->addVertex($controller)
                ->addVertex($getListRequest)
                ->addVertex($createRequest)
                ->addVertex($modifyRequest)
                ->addVertex($detailRequest)
                ->addVertex($removeRequest)
                ->addVertex($getListResponse)
                ->addVertex($detailResponse)
                ->addEdge($daoInterface, $dao)
                ->addEdge($error, $service)
                ->addEdge($dao, $service)
                ->addEdge($serviceInterface, $service)
                ->addEdge($getListRequest, $logic)
                ->addEdge($createRequest, $logic)
                ->addEdge($modifyRequest, $logic)
                ->addEdge($detailRequest, $logic)
                ->addEdge($removeRequest, $logic)
                ->addEdge($getListResponse, $logic)
                ->addEdge($detailResponse, $logic)
                ->addEdge($service, $logic)
                ->addEdge($logic, $controller)
                ->run();
        }
    }
}